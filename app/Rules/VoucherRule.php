<?php

namespace App\Rules;

use App\Voucher;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class VoucherRule implements Rule
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
        $auth_id = Auth::id();

        $voucher = Voucher::find($id);

        if ($voucher == null) {

            $this->message = 'Voucher not found.';

            return false;

        }

        if (Auth::guard('companies')->check() && $voucher->company_id != $auth_id) {

            $this->message = "this voucher does not belong to company_id $auth_id.";

            return false;

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
        return $this->message;
    }
}
