<?php

namespace App\Rules;

use App\Order;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class OrderRule implements Rule
{
    /**
     * Error message.
     *
     * @return void
     */
    private $message;

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

        $order = Order::find($id);

        if ($order == null) {

            $this->message = 'Order not found.';

            return false;

        }

        if (Auth::guard('users')->check() && $order->user_id != $auth_id) {

            $this->message = "this order does not belong to user_id $auth_id.";

            return false;

        }

        if (Auth::guard('companies')->check() && $order->company_id != $auth_id) {

            $this->message = "this order does not belong to company_id $auth_id.";

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
