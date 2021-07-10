<?php

namespace App\Models;

use App\Casts\Image;
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
		'price',
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
		'company_id' 	=> 'integer',
		'segment_id' 	=> 'integer',
		'has_sunday' 	=> 'boolean',
		'has_monday' 	=> 'boolean',
		'has_tuesday' 	=> 'boolean',
		'has_wednesday' => 'boolean',
		'has_thursday' 	=> 'boolean',
		'has_friday' 	=> 'boolean',
		'has_saturday' 	=> 'boolean',
		'price' 		=> 'float',
		'rebate' 		=> 'float',
		'status' 		=> 'boolean',
		'image'			=> Image::class
	];

	/**
     * Get the company that owns the product.
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
	}

	/**
     * Get the segment that owns the product.
     */
    public function segment()
    {
        return $this->belongsTo('App\Models\Segment');
	}

	/**
     * Get the complements for the product.
     */
    public function complements()
    {
        return $this->hasMany('App\Models\Complement');
    }
}
