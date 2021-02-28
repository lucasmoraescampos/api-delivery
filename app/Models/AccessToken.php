<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    /**
	 * The status constants.
	 */
	const STATUS_INVALID = 0;

	const STATUS_VALID = 1;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'token',
		'ip',
		'device',
		'platform',
		'browser',
		'status'
	];

	/**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => true
	];

	/**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
		'user_id' => 'integer'
	];
}
