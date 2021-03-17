<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'user_id',
        'street_name',
        'street_number',
        'complement',
        'district',
        'city',
        'uf',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}
