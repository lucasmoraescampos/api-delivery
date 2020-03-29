<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AvailableShiftRule implements Rule
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
    public function passes($attribute, $value)
    {
        return $value > 0 && $value < 5;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Turno disponível inválido. Os turnos disponíveis são: 1 => matutino; 2 => vespertino; 3 => noturno; 4 => sempre dispónivel';
    }
}
