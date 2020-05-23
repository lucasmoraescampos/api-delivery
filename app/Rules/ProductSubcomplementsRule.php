<?php

namespace App\Rules;

use App\Subcomplement;
use Illuminate\Contracts\Validation\Rule;

class ProductSubcomplementsRule implements Rule
{
    private $product_id;

    private $error_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $subcomplements)
    {
        foreach ($subcomplements as $subcomplement) {

            if ($subcomplement['amount'] < 1) {

                return false;

            }

            if (Subcomplement::where('id', $subcomplement['id'])->count() == 0) {

                $this->error_id = $subcomplement['id'];

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
        if ($this->error_id) {
            return 'ID subcomplemento ' . $this->error_id . ' inválido';
        }

        return 'Quantidade deve ser maior do que 0';
    }
}
