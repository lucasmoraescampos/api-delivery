<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'company_id', 'user_id', 'code', 'qty', 'value', 'min_value', 'expiration_date', 'status'
    ];

    protected $attributes = [
        'status' => ACTIVE
    ];

    public static function checkCode($code)
    {
        return Voucher::where('code', strtolower($code))->count() > 0;
    }

    public static function findUser($user)
    {
        return User::where('phone', $user)->orWhere('email', $user)->first();
    }
}
