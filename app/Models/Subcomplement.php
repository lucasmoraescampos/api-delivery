<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcomplement extends Model
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
        'complement_id',
		'description',
        'price'
	];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'complement_id' => 'integer',
        'price' => 'float'
    ];
    

	/**
     * Get the complement that owns the subcomplement.
     */
    public function complement()
    {
        return $this->belongsTo('App\Models\Complement');
	}
}
