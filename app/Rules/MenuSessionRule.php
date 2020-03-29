<?php

namespace App\Rules;

use App\MenuSession;
use Illuminate\Contracts\Validation\Rule;

class MenuSessionRule implements Rule
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
        return MenuSession::where('id', $id)->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Menu Session não encontrado.';
    }
}
