<?php

namespace App\Rules;

use App\Subcomplement;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SubcomplementRule implements Rule
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
        return Subcomplement::from('subcomplements as s')
            ->leftJoin('complements as c', 'c.id', 's.complement_id')
            ->leftJoin('products as p', 'p.id', 'c.product_id')
            ->where('s.id', $id)
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
        return 'Subcomplement ID not authorized.';
    }
}
