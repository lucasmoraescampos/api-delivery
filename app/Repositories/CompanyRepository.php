<?php

namespace App\Repositories;

use App\Events\CompanyStatus;
use App\Exceptions\CustomException;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyPaymentMethod;
use App\Models\Favorite;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Rules\DataUrlImageRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    /**
     * CompanyRepository constructor.
     *
     * @param Company $model
     */
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $attributes
     * @return Collection
     */
    public function getFavorites(array $attributes): Collection
    {
        $this->validateGetFavorites($attributes);

        $user = Auth::user();

        return $user->favorites()
            ->select('id', 'category_id', 'slug', 'image', 'name', 'evaluation', 'delivery_time', 'delivery_price', 'open')
            ->distance($attributes['latitude'], $attributes['longitude'], false)
            ->orderBy('open', 'desc')
            ->get();
    }

    /**
     * @param array $attributes
     * @return Company
     */
    public function create(array $attributes): Company
    {
        $this->validateCreate($attributes);

        $company = new Company(Arr::only($attributes, [
            'name',
            'category_id',
            'phone',
            'document_number',
            'postal_code',
            'latitude',
            'longitude',
            'street_name',
            'street_number',
            'district',
            'complement',
            'uf',
            'city',
            'plan_id',
            'allow_payment_online',
            'allow_payment_delivery',
            'allow_takeout',
            'min_order_value',
            'delivery_time',
            'delivery_price',
            'radius'
        ]));

        $company->user_id = Auth::id();

        $company->slug = $this->generateSlug($company->name);

        $company->image = dataUrlImageUpload($attributes['image'], 'companies');

        if (isset($attributes['banner'])) {
            
            $company->banner = dataUrlImageUpload($attributes['banner'], 'companies/banners');

        }

        $company->save();

        $this->signPlan($company->id, $company->plan_id);

        if (isset($attributes['payment_methods'])) {

            $this->insertPaymentMethods($company->id, $attributes['payment_methods']);
            
        }

        $company->load('plan');

        $company->load('payment_methods');

        return $company;
    }

    /**
     * @param array $attributes
     * @param mixed $id
     * @return Company
     */
    public function update($id, array $attributes): Company
    {
        $this->validateUpdate($attributes);

        $company = Company::find($id);

        if ($company == null) {
            throw new CustomException('Empresa n??o existe.', 404);
        }

        if (Auth::user()->is_admin == false) {

            if ($company->user_id != Auth::id()) {
                throw new CustomException('Empresa n??o autorizada.', 401);
            }

            if ($company->status == Company::STATUS_INACTIVE) {
                throw new CustomException('Empresa em an??lise.', 403);
            }
            
            if ($company->status == Company::STATUS_SUSPENDED) {
                throw new CustomException('Empresa suspensa.', 403);
            }

        }

        $company->fill(Arr::only($attributes, [
            'name',
            'category_id',
            'phone',
            'document_number',
            'postal_code',
            'latitude',
            'longitude',
            'street_name',
            'street_number',
            'district',
            'complement',
            'uf',
            'city',
            'plan_id',
            'allow_payment_online',
            'allow_payment_delivery',
            'allow_takeout',
            'min_order_value',
            'delivery_time',
            'delivery_price',
            'radius',
            'open'
        ]));

        if (isset($attributes['image'])) {
            $company->image = dataUrlImageUpload($attributes['image'], 'companies');
        }

        if (isset($attributes['banner'])) {
            $company->banner = dataUrlImageUpload($attributes['banner'], 'companies/banners');
        }

        if (isset($attributes['slug'])) {
            $company->slug = strtolower($attributes['slug']);
        }

        if (Auth::user()->is_admin && isset($attributes['status'])) {

            $company->status = $attributes['status'];

            CompanyStatus::dispatch($company);

        }

        $company->save();

        if (isset($attributes['plan_id'])) {
            $this->signPlan($company->id, $company->plan_id);
        }

        if (isset($attributes['payment_methods'])) {
            $this->insertPaymentMethods($company->id, $attributes['payment_methods']);
        }

        if (isset($attributes['allow_payment_delivery']) && $attributes['allow_payment_delivery'] == false) {
            $this->deletePaymentMethodsByCompany($company->id);
        }

        $company->load('plan');

        $company->load('payment_methods');

        return $company;
    }

    /**
     * @param mixed $id
     * @return void
     */
    public function delete($id): void
    {
        $company = Company::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$company) {
            throw new CustomException('Empresa n??o encontrada.', 422);
        }

        if ($company->status == Company::STATUS_INACTIVE) {
            throw new CustomException('Empresa em an??lise.', 403);
        }
        
        if ($company->status == Company::STATUS_SUSPENDED) {
            throw new CustomException('Empresa suspensa.', 403);
        }

        $company->deleted = true;

        $company->deleted_at = date('Y-m-d H:i:s');

        $company->save();
    }

    /**
     * @param array $attributes
     * @return Favorite
     */
    public function createFavorite(array $attributes): Favorite
    {
        $this->validateCreateFavorite($attributes);

        $user_id = Auth::id();

        if (Company::where('id', $attributes['company_id'])->count() == 0) {
            throw new CustomException('Company id not found.', 404);
        }

        $favorite = Favorite::where('user_id', $user_id)
            ->where('company_id', $attributes['company_id'])
            ->first();

        return $favorite ?? Favorite::create([
            'user_id'    => $user_id,
            'company_id' => $attributes['company_id']
        ]);
    }

    /**
     * @param mixed $company_id
     * @return void
     */
    public function deleteFavorite($company_id): void
    {
        Favorite::where('user_id', Auth::id())
            ->where('company_id', $company_id)
            ->delete();
    }

    /**
     * @param mixed $name
     * @return string
     */
    private function generateSlug($name)
    {
        $slug = Str::slug($name);

        if (preg_match('/^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/', $slug) == false) {
            $slug = uniqid('mp-');
        }

        if (Company::where('slug', $slug)->count() == 0) {
            return $slug;
        }
        
        for ($i = 1; true; $i++) {

            $slug = uniqid('mp-');

            if (Company::where('slug', $slug)->count() == 0) {
                return $slug;
            }
        }
    }

    /**
     * @param mixed $company_id
     * @param mixed $plan_id
     * @return void
     */
    private function signPlan($company_id, $plan_id): void
    {
        PlanSubscription::where('company_id', $company_id)->update(['status' => false]);

        PlanSubscription::create(['company_id' => $company_id, 'plan_id' => $plan_id]);   
    }

    /**
     * @param mixed $company_id
     * @param array $payment_methods
     * @return void
     */
    private function insertPaymentMethods($company_id, array $payment_methods): void
    {
        $this->deletePaymentMethodsByCompany($company_id);

        $company_payment_methods = [];

        foreach ($payment_methods as $payment_method_id) {
            $company_payment_methods[] = [
                'company_id' => $company_id,
                'payment_method_id' => $payment_method_id
            ];
        }

        CompanyPaymentMethod::insert($company_payment_methods);
    }

    /**
     * @param mixed $company_id
     * @return void
     */
    private function deletePaymentMethodsByCompany($company_id): void
    {
        CompanyPaymentMethod::where('company_id', $company_id)->delete();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateCreate(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:11',
            'postal_code' => 'required|string|max:20',
            'latitude' => 'required|string|max:40',
            'longitude' => 'required|string|max:40',
            'street_name' => 'required|string|max:255',
            'street_number' => 'required|string|max:20',
            'district' => 'required|string|max:100',
            'uf' => 'required|string|max:2',
            'city' => 'required|string|max:100',
            'allow_payment_online' => 'required|boolean',
            'allow_payment_delivery' => 'required|boolean',
            'allow_takeout' => 'required|boolean',
            'min_order_value' => 'required|numeric',
            'delivery_time' => 'required|numeric',
            'delivery_price' => 'required|numeric',
            'radius' => 'required|numeric',
            'payment_methods' => 'required_if:allow_payment_delivery,1|array',
            'image' => ['required', new DataUrlImageRule()],
            'banner' => ['nullable', new DataUrlImageRule()],
            'document_number' => [
                'required', 'string', 'max:14', function ($attribute, $value, $fail) {
                    if (validateDocumentNumber($value) == false) {
                        $fail('N??mero de documento inv??lido.');
                    }
                }
            ],
            'category_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) {
                    if (Category::where('id', $value)->count() == 0) {
                        $fail('Categoria n??o encontrada.');
                    }
                }
            ],
            'plan_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) use ($attributes) {

                    $count = Plan::where('id', $value)
                        ->where('category_id', $attributes['category_id'])
                        ->where('status', true)
                        ->count();

                    if ($count == 0) {
                        $fail('Plano n??o encontrado.');
                    }
                }
            ],
            'payment_methods.*' => [
                'required', 'numeric', function ($attribute, $value, $fail) {
                    if (PaymentMethod::where('id', $value)->count() == 0) {
                        $fail('M??todo de pagamento n??o encontrado.');
                    }
                }
            ]
        ]);

        $validator->validate();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateUpdate(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:11',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|string|max:40',
            'longitude' => 'nullable|string|max:40',
            'street_name' => 'nullable|string|max:255',
            'street_number' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'city' => 'nullable|string|max:100',
            'allow_payment_online' => 'nullable|boolean',
            'allow_payment_delivery' => 'nullable|boolean',
            'allow_takeout' => 'nullable|boolean',
            'min_order_value' => 'nullable|numeric',
            'delivery_time' => 'nullable|numeric',
            'delivery_price' => 'nullable|numeric',
            'radius' => 'nullable|numeric',
            'open' => 'nullable|boolean',
            'payment_methods' => 'nullable|array',
            'image' => ['nullable', new DataUrlImageRule()],
            'banner' => ['nullable', new DataUrlImageRule()],
            'status' => [
                'nullable', 'numeric', function ($attribute, $value, $fail) {
                    if (!in_array($value, [Company::STATUS_INACTIVE, Company::STATUS_ACTIVE, Company::STATUS_SUSPENDED])) {
                        $fail('status inv??lido.');
                    }
                }
            ],
            'slug' => [
                'nullable', 'string', function ($attribute, $value, $fail) {
                    if (preg_match('/^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/', $value) == false) {
                        $fail('Slug inv??lido.');
                    }
                }
            ],
            'document_number' => [
                'nullable', 'string', 'max:15', function ($attribute, $value, $fail) {
                    if (validateDocumentNumber($value) == false) {
                        $fail('N??mero de documento inv??lido.');
                    }
                }
            ],
            'category_id' => [
                'nullable', 'numeric', function ($attribute, $value, $fail) {
                    if (Category::where('id', $value)->count() == 0) {
                        $fail('Categoria n??o encontrada.');
                    }
                }
            ],
            'plan_id' => [
                'nullable', 'numeric', function ($attribute, $value, $fail) use ($attributes) {
                    if (!isset($attributes['category_id'])) {
                        $fail('Categoria ?? obrigat??ria na altera????o do plano.');
                    }
                    else if (Plan::where('id', $value)->where('category_id', $attributes['category_id'])->count() == 0) {
                        $fail('Plano n??o encontrado.');
                    }
                }
            ],
            'payment_methods.*' => [
                'nullable', 'numeric', function ($attribute, $value, $fail) {
                    if (PaymentMethod::where('id', $value)->count() == 0) {
                        $fail('M??todo de pagamento n??o encontrado.');
                    }
                }
            ]
        ]);

        $validator->validate();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateGetFavorites(array $attributes): void 
    {
        $validator = Validator::make($attributes, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $validator->validate();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateCreateFavorite(array $attributes): void 
    {
        $validator = Validator::make($attributes, [
            'company_id' => 'required|numeric'
        ]);

        $validator->validate();
    }
}
