<?php

namespace App\Rules;

use App\MenuSession;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class MenuSessionRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->unauthorized = false;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $data
     * @return bool
     */
    public function passes($attribute, $data)
    {
        if (is_array($data)) {

            foreach ($data as $session) {

                if ($session['company_id'] != Auth::id()) {

                    $this->unauthorized = true;

                    return false;

                }

                $count = MenuSession::where('id', $session['id'])
                    ->where('company_id', $session['company_id'])
                    ->count();

                if ($count == 0) {

                    $this->id = $session['id'];

                    return false;

                }

            }

            return true;

        }

        else {

            return MenuSession::where('id', $data)
                ->where('company_id', Auth::id())
                ->count() > 0;

        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->unauthorized) {
            return 'unauthorized.';
        }

        if ($this->id) {
            return "session id $this->id not found.";
        }

        else {
            return "session id not found.";
        }
        
    }
}
