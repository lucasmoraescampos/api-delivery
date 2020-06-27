<?php

namespace App\Rules;

use App\PaymentMethod;
use Illuminate\Contracts\Validation\Rule;

class PaymentMethodsRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $payment_methods)
    {
        $search = PaymentMethod::all();

        $data = [];

        foreach ($search as $s) {

            $data[] = $s->id;

        }

        foreach ($payment_methods as $pm) {

            if (!in_array($pm, $data)){

                return false;

            }

        }

        return true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Payment method ID not found.';
    }
}
