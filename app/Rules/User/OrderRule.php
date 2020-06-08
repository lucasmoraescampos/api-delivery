<?php

namespace App\Rules\User;

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
        $order = Order::find($id);

        $user_id = Auth::id();

        if ($order == null) {

            $this->message = 'Order not found.';

            return false;

        }

        if ($order->user_id != $user_id) {

            $this->message = "this order does not belong to user_id $user_id.";

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
