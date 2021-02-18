<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\CompanyPaymentMethod;
use App\Models\PaymentMethod;
use App\Models\UserCompany;
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
     * @param mixed $company_id
     * @return boolean
     */
    public static function checkAuth($company_id): bool
    {
        return UserCompany::where('user_id', Auth::id())
            ->where('company_id', $company_id)
            ->count() > 0;
    }

    /**
     * @param array $attributes
     * @return Company
     */
    public function create(array $attributes): Company
    {
        $this->validateCreate($attributes);

        $company = new Company([
            'name' => $attributes['name'],
            'phone' => $attributes['phone'],
            'document_number' => $attributes['document_number'],
            'postal_code' => $attributes['postal_code'],
            'latitude' => $attributes['latitude'],
            'longitude' => $attributes['longitude'],
            'street_name' => $attributes['street_name'],
            'street_number' => $attributes['street_number'],
            'district' => $attributes['district'],
            'uf' => $attributes['uf'],
            'city' => $attributes['city'],
            'allow_payment_online' => $attributes['allow_payment_online'],
            'allow_payment_delivery' => $attributes['allow_payment_delivery'],
            'allow_withdrawal_local' => $attributes['allow_withdrawal_local'],
            'min_order_value' => $attributes['min_order_value'],
            'waiting_time' => $attributes['waiting_time'],
            'delivery_price' => $attributes['delivery_price'],
            'radius' => $attributes['radius']            
        ]);

        $company->slug = $this->createSlug($company->name);

        $company->image = fileUpload($attributes['image'], 'companies');

        $company->save();

        UserCompany::create([
            'user_id' => Auth::id(),
            'company_id' => $company->id
        ]);

        $this->insertPaymentMethods($company->id, $attributes['payment_methods']);

        $company->load('payment_methods');

        return $company;
    }

    /**
     * @param mixed $name
     * @return string
     */
    private function createSlug($name)
    {
        $slug = Str::slug($name);

        $count = Company::where('slug', $slug)->count();

        if ($count == 0) {
            return $slug;
        }
        
        for ($i = 1; true; $i++) {

            $str = $slug . ' ' . $i;

            $count = Company::where('slug', $str)->count();

            if ($count == 0) {
                return $str;
            }
        }
    }

    /**
     * @param mixed $company_id
     * @param array $payment_methods
     * @return void
     */
    private function insertPaymentMethods($company_id, array $payment_methods): void
    {
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
     * @param array $attributes
     * @return void
     */
    private function validateCreate(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'image' => 'required|file|mimes:gif,png,jpeg,bmp,webp',
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:11',
            'document_number' => 'required|string|max:20',
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
            'payment_methods' => [
                'required_if:allow_payment_delivery,1', 'array',
                function ($attribute, $value, $fail) {
                    $payment_methods = PaymentMethod::all()->pluck('id');
                    foreach ($value as $v) {
                        if ($payment_methods->search($v) === false) {
                            return $fail("Método de pagamento $v não encontrado.");
                        }
                    }
                }
            ]
        ]);

        $validator->validate();
    }
}
