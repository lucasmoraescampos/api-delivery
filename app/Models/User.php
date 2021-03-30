<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * Constants.
     */
    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 1;

    const STATUS_BLOCKED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
        'name',
        'email',
        'phone',
        'password',
        'image'
    ];

        /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::STATUS_INACTIVE,
        'is_admin' => false
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'is_admin'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the companies for the user.
     */
    public function companies()
    {
        return $this->hasMany('App\Models\Company');
    }

    /**
     * Get the cards for the user.
     */
    public function cards()
    {
        return $this->hasMany('App\Models\Card');
    }
}
