<?php

namespace App\Repositories;

use App\Exceptions\CustomException;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyPaymentMethod;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\PlanSubscription;
use Illuminate\Support\Arr;
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
            'allow_withdrawal_local',
            'min_order_value',
            'waiting_time',
            'delivery_price',
            'radius'
        ]));

        $company->user_id = Auth::id();

        $company->slug = $this->generateSlug($company->name);

        $company->image = fileUpload($attributes['image'], 'companies');

        if (isset($attributes['banner'])) {
            
            $company->banner = fileUpload($attributes['banner'], 'companies/banners');

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

        $company = Company::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$company) {
            throw new CustomException('Empresa não encontrada.', 422);
        }

        if ($company->status == Company::STATUS_INACTIVE) {
            throw new CustomException('Empresa em análise.', 403);
        }
        
        if ($company->status == Company::STATUS_SUSPENDED) {
            throw new CustomException('Empresa suspensa.', 403);
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
            'allow_withdrawal_local',
            'min_order_value',
            'waiting_time',
            'delivery_price',
            'radius',
            'open'
        ]));

        if (isset($attributes['image'])) {
            $company->image = fileUpload($attributes['image'], 'products');
        }

        if (isset($attributes['banner'])) {
            $company->banner = fileUpload($attributes['banner'], 'companies/banners');
        }

        if (isset($attributes['slug'])) {
            $company->slug = strtolower($attributes['slug']);
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
            throw new CustomException('Empresa não encontrada.', 422);
        }

        if ($company->status == Company::STATUS_INACTIVE) {
            throw new CustomException('Empresa em análise.', 403);
        }
        
        if ($company->status == Company::STATUS_SUSPENDED) {
            throw new CustomException('Empresa suspensa.', 403);
        }

        $company->deleted = true;

        $company->deleted_at = date('Y-m-d H:i:s');

        $company->save();
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
            'image' => 'required|file|mimes:gif,png,jpeg,bmp,webp',
            'banner' => 'nullable|file|mimes:gif,png,jpeg,bmp,webp',
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
            'allow_withdrawal_local' => 'required|boolean',
            'min_order_value' => 'required|numeric',
            'waiting_time' => 'required|numeric',
            'delivery_price' => 'required|numeric',
            'radius' => 'required|numeric',
            'payment_methods' => 'required_if:allow_payment_delivery,1|array',
            'document_number' => [
                'required', 'string', 'max:15', function ($attribute, $value, $fail) {
                    if (validateDocumentNumber($value) == false) {
                        $fail('Número de documento inválido.');
                    }
                }
            ],
            'category_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) {
                    if (Category::where('id', $value)->count() == 0) {
                        $fail('Categoria não encontrada.');
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
                        $fail('Plano não encontrado.');
                    }
                }
            ],
            'payment_methods.*' => [
                'required', 'numeric', function ($attribute, $value, $fail) {
                    if (PaymentMethod::where('id', $value)->count() == 0) {
                        $fail('Método de pagamento não encontrado.');
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
            'image' => 'nullable|file|mimes:gif,png,jpeg,bmp,webp',
            'banner' => 'nullable|file|mimes:gif,png,jpeg,bmp,webp',
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
            'allow_withdrawal_local' => 'nullable|boolean',
            'min_order_value' => 'nullable|numeric',
            'waiting_time' => 'nullable|numeric',
            'delivery_price' => 'nullable|numeric',
            'radius' => 'nullable|numeric',
            'open' => 'nullable|boolean',
            'payment_methods' => 'nullable|array',
            'slug' => [
                'nullable', 'string', function ($attribute, $value, $fail) {
                    if (preg_match('/^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/', $value) == false) {
                        $fail('Slug inválido.');
                    }
                }
            ],
            'document_number' => [
                'nullable', 'string', 'max:15', function ($attribute, $value, $fail) {
                    if (validateDocumentNumber($value) == false) {
                        $fail('Número de documento inválido.');
                    }
                }
            ],
            'category_id' => [
                'nullable', 'numeric', function ($attribute, $value, $fail) {
                    if (Category::where('id', $value)->count() == 0) {
                        $fail('Categoria não encontrada.');
                    }
                }
            ],
            'plan_id' => [
                'nullable', 'numeric', function ($attribute, $value, $fail) use ($attributes) {
                    if (Plan::where('id', $value)->where('category_id', $attributes['category_id'])->count() == 0) {
                        $fail('Plano não encontrado.');
                    }
                }
            ],
            'payment_methods.*' => [
                'nullable', 'numeric', function ($attribute, $value, $fail) {
                    if (PaymentMethod::where('id', $value)->count() == 0) {
                        $fail('Método de pagamento não encontrado.');
                    }
                }
            ]
        ]);

        $validator->validate();
    }
}
