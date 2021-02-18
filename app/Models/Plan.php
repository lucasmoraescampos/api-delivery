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
        'name',
        'digital_menu',
        'limit_companies',
        'price',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'digital_menu' => 'boolean',
        'price' => 'float',
        'status' => 'boolean'
    ];
}
