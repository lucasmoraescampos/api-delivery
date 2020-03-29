<?php

namespace App\Rules;

use App\Subcategory;
use Illuminate\Contracts\Validation\Rule;

class SubcategoryRule implements Rule
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
        return Subcategory::where('id', $id)->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'ID Subcategoria não existe.';
    }
}
