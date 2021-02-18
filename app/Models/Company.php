<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Company extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'companies';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'name',
        'phone',
        'document_number',
        'postal_code',
        'latitude',
        'longitude',
        'street_name',
        'street_number',
        'district',
        'uf',
        'city',
        'allow_payment_online',
        'allow_payment_delivery',
        'allow_withdrawal_local',
        'min_order_value',
        'waiting_time',
        'delivery_price',
        'radius',
        'slug',
        'image'
	];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'balance' => 0.00,
        'is_delivery_open' => false,
        'status' => true
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'float',
        'is_delivery_open' => 'boolean',
        'status' => 'integer',
        'evaluation' => 'float',
        'waiting_time' => 'integer',
        'delivery_price' => 'float',
        'min_order_value' => 'float',
        'radius' => 'integer',
        'allow_payment_delivery' => 'boolean',
        'allow_payment_online' => 'boolean',
        'allow_withdrawal_local' => 'boolean'
    ];

	/**
     * Get the payment methods for the company.
     */
    public function payment_methods()
    {
        return $this->belongsToMany('App\PaymentMethod', 'companies_payment_methods', 'company_id', 'payment_method_id');
    }

    /**
     * @param string $slug
     * @return Company
     */
    public static function slug(string $slug): Company
    {
        return Company::select('companies.*')
            ->leftJoin('users_companies', 'users_companies.company_id', 'companies.id')
            ->where('users_companies.user_id', Auth::id())
            ->where('companies.slug', $slug)
            ->firstOrFail();
    }
}
