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
        'accept_payment_app' => 0,
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

    public static function getByCategory($category_id)
    {
        return Company::select('id', 'photo', 'name', 'waiting_time', 'latitude', 'longitude', 'delivery_price', 'is_open')
            ->where('category_id', $category_id)
            ->whereIn('id', function ($query) {

                $query->select('company_id')
                    ->from(with(new Product)->getTable())
                    ->distinct();
                    
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('is_open', 'desc')
            ->get();
    }

    public static function getBySubcategory($subcategory_id)
    {
        return Company::select('id', 'photo', 'name', 'waiting_time', 'latitude', 'longitude', 'delivery_price', 'is_open')
            ->whereIn('id', function ($query) use ($subcategory_id) {

                $query->select('company_id')
                    ->from(with(new Product)->getTable())
                    ->where('subcategory_id', $subcategory_id)
                    ->distinct();
                    
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('is_open', 'desc')
            ->get();
    }
}
