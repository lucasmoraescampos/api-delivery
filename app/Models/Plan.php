<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'category_id',
        'name',
        'fee',
        'online_payment_fee',
        'delivery_type',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'fee' => 'float',
        'online_payment_fee' => 'float',
        'delivery_type' => 'integer',
        'status' => 'boolean'
    ];
}
