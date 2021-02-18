<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'company_id',
		'segment_id',
		'name',
		'description',
		'qty',
		'price',
		'cost',
		'rebate',
		'has_sunday',
		'has_monday',
		'has_tuesday',
		'has_wednesday',
		'has_thursday',
		'has_friday',
		'has_saturday',
		'start_time',
		'end_time',
		'image',
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
		'company_id' => 'integer',
		'segment_id' => 'integer',
		'has_sunday' => 'boolean',
		'has_monday' => 'boolean',
		'has_tuesday' => 'boolean',
		'has_wednesday' => 'boolean',
		'has_thursday' => 'boolean',
		'has_friday' => 'boolean',
		'has_saturday' => 'boolean',
		'qty' => 'numeric',
		'price' => 'float',
		'cost' => 'float',
		'rebate' => 'float',
		'status' => 'boolean'
	];

	/**
     * Get the company that owns the product.
     */
    public function company()
    {
        return $this->belongsTo('App\Company');
	}

	/**
     * Get the segment that owns the product.
     */
    public function segment()
    {
        return $this->belongsTo('App\Segment');
	}
}
