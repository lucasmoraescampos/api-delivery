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
        'company_id',
        'plan_id'
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
		'company_id' => 'integer',
		'plan_id' => 'integer',
		'status' => 'boolean'
	];

    /**
	 * Get the plan that owns the plan subscription.
	 */
	public function plan()
	{
		return $this->belongsTo('App\Models\Plan');
    }
}
