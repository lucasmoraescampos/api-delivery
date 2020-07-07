<?php

namespace App\Rules;

use App\Product;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProductRule implements Rule
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
    public function passes($attribute, $id)
    {
        if (Auth::guard('companies')->check()) {

            return Product::where('id', $id)
                ->where('company_id', Auth::id())
                ->count() > 0;

        }

        return Product::where('id', $id)->count() > 0;

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
