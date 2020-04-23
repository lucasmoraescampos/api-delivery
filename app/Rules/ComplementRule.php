<?php

namespace App\Rules;

use App\Complement;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ComplementRule implements Rule
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
        return Complement::from('complements as c')
            ->where('c.id', $id)
            ->leftJoin('products as p', 'p.id', 'c.product_id')
            ->where('p.company_id', Auth::id())
            ->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Complement ID not authorized.';
    }
}
