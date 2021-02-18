<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanSubscription extends Model
{
    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'user_id',
        'plan_id',
        'payment_id',
        'transaction_amount',
        'transaction_fee',
        'expiration'
    ];

    /**
	 * The model's default values for attributes.
	 *
	 * @var array
	 */
	protected $attributes = [
        'status' =>  true
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'transaction_amount' => 'float',
        'transaction_fee' => 'float',
        'status' => 'boolean'
    ];

    /**
	 * Get the plan that owns the plan subscription.
	 */
	public function plan()
	{
		return $this->belongsTo('App\Plan');
    }
}
