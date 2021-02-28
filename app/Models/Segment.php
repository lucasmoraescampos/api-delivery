<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
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
        'company_id',
		'name',
		'position'
	];

	/**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'position',
    ];

	/**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
		'company_id' => 'integer',
		'position' => 'integer'
	];

	/**
	 * Get the company that owns the segment.
	 */
	public function company()
	{
		return $this->belongsTo('App\Models\Company');
    }

	/**
     * Get the products for the segment.
     */
    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }
}
