<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OrderCompanyRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->checkCompany();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O pedido possui produtos de empresas diferentes.';
    }

    public function checkCompany()
    {
        $company_id = null;

        foreach ($this->products as $product) {

            if ($company_id === null) {

                $company_id = $product['company_id'];

            }

            elseif ($company_id != $product['company_id']) {

                return false;

            }
            
        }

        return true;

    }
}
