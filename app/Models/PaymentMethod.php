<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'payment_methods';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'icon'
	];

	/**
	 * Get the companies for the payment method.
	 */
	public function companies()
	{
        return $this->belongsToMany('App\Models\Company', 'companies_payment_methods', 'payment_method_id', 'company_id');
    }
}
