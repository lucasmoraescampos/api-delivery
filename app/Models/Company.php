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
        'user_id',
        'category_id',
        'plan_id',
        'name',
        'phone',
        'document_number',
        'slug',
        'waiting_time',
        'delivery_price',
        'min_order_value',
        'radius',
        'allow_payment_delivery',
        'allow_payment_online',
        'allow_withdrawal_local',
        'image',
        'banner',
        'street_name',
        'street_number',
        'complement',
        'district',
        'city',
        'uf',
        'postal_code',
        'country',
        'latitude',
        'longitude'
	];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'balance' => 0.00,
        'open' => false,
        'deleted' => false
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'category_id' => 'integer',
        'plan_id' => 'integer',
        'balance' => 'float',
        'open' => 'boolean',
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
     * Get the plan that owns the company.
     */
    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
	}

	/**
     * Get the payment methods for the company.
     */
    public function payment_methods()
    {
        return $this->belongsToMany('App\Models\PaymentMethod', 'companies_payment_methods', 'company_id', 'payment_method_id');
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
