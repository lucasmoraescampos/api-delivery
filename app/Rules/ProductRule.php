<?php

namespace App\Rules;

use App\Product;
use Illuminate\Contracts\Validation\Rule;

class ProductRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($company_id = null)
    {
        $this->company_id = $company_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $id)
    {
        if ($this->company_id === null) {

            return Product::where('id', $id)->count() > 0;

        }

        return Product::where('id', $id)
            ->where('company_id', $id)
            ->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Produto não encontrado.';
    }
}
