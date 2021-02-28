<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complement extends Model
{
    /**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'product_id',
		'title',
        'qty_min',
        'qty_max',
        'required'
	];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
		'product_id' => 'integer',
		'required' => 'boolean'
	];

    /**
     * Get the product that owns the complement.
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
	}

    /**
     * Get the subcomplements for the complement.
     */
    public function subcomplements()
    {
        return $this->hasMany('App\Models\Subcomplement');
    }
}
