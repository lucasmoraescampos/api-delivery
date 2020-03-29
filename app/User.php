<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'surname', 'email', 'phone', 'password', 'status', 'temporary_code'
    ];

    protected $attributes = [
        'status' => ACTIVE
    ];

    protected $hidden = [
        'password', 'temporary_code'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function generateCode()
    {
        $invalid = [
            11111,
            22222,
            33333,
            44444,
            55555,
            66666,
            77777,
            88888,
            99999,
            12345,
            54321
        ];

        while ($code = sprintf("%06d", mt_rand(10000, 99999))) {

            if (!array_search($code, $invalid)) {
                break;
            }

        }

        $this->code = $code . ';' . date('Y-m-d H:i:s');

        $this->save();
    }
    
    public function generateJWT()
    {
        return JWTAuth::fromUser($this);
    }
}
