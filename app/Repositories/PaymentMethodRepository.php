<?php

namespace App\Repositories;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Validator;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodRepositoryInterface
{
    /**
     * PaymentMethodRepository constructor.
     *
     * @param PaymentMethod $model
     */
    public function __construct(PaymentMethod $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $attributes
     * @return PaymentMethod
     */
    public function create(array $attributes): PaymentMethod
    {
        $validator = Validator::make($attributes, [
            'name' => 'required|string|max:40',
            'icon' => 'nullable|file|max:8000|mimes:svg'
        ]);

        $validator->validate();

        $paymentMethod = new PaymentMethod(['name' => $attributes['name']]);

        $paymentMethod->icon = fileUpload($attributes['icon'], 'paymentMethods');

        $paymentMethod->save();

        return $paymentMethod;
    }
}
