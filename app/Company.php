<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Company extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'companies';

    protected $fillable = [
        'category_id', 'name', 'email', 'phone', 'password'
    ];

    protected $attributes = [
        'status' => WAITING,
        'is_open' => 0
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getQtyMenuSessions()
    {
        return MenuSession::where('company_id', $this->id)->count();
    }
}
