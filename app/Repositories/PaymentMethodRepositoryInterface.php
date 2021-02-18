<?php

namespace App\Repositories;

use App\Models\PaymentMethod;

interface PaymentMethodRepositoryInterface
{
    /**
     * @param array $attributes
     * @return PaymentMethod
     */
    public function create(array $attributes): PaymentMethod;
}
