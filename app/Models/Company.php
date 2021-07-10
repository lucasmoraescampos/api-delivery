<?php

namespace App\Models;

use App\Casts\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    /**
     * Constants.
     */
    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 1;

    const STATUS_SUSPENDED = 2;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'companies';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'user_id',
        'category_id',
        'plan_id',
        'name',
        'phone',
        'document_number',
        'slug',
        'delivery_time',
        'delivery_price',
        'min_order_value',
        'radius',
        'allow_payment_delivery',
        'allow_payment_online',
        'allow_takeout',
        'image',
        'banner',
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
        'open',
        'status'
	];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'balance'   => 0.00,
        'open'      => false,
        'status'    => self::STATUS_INACTIVE,
        'deleted'   => false
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id'                   => 'integer',
        'category_id'               => 'integer',
        'plan_id'                   => 'integer',
        'balance'                   => 'float',
        'open'                      => 'boolean',
        'status'                    => 'integer',
        'evaluation'                => 'float',
        'delivery_time'             => 'integer',
        'delivery_price'            => 'float',
        'min_order_value'           => 'float',
        'radius'                    => 'float',
        'allow_payment_delivery'    => 'boolean',
        'allow_payment_online'      => 'boolean',
        'allow_takeout'             => 'boolean',
        'banner'                    => Image::class,
        'image'                     => Image::class
    ];

    /**
     * Get the plan that owns the company.
     */
    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
	}

	/**
     * Get the payment methods for the company.
     */
    public function payment_methods()
    {
        return $this->belongsToMany('App\Models\PaymentMethod', 'companies_payment_methods', 'company_id', 'payment_method_id');
    }

    /**
     * calculates user distance when obtaining the list of administrators.
     */
    public static function scopeDistance($query, $latitude, $longitude, $radius = true)
    {
        $select = "111.045
            * DEGREES(ACOS(LEAST(1.0, COS(RADIANS($latitude))
            * COS(RADIANS(latitude))
            * COS(RADIANS($longitude) - RADIANS(longitude))
            + SIN(RADIANS($latitude))
            * SIN(RADIANS(latitude))))) AS distance";

        if ($radius) {

            $where = "latitude BETWEEN $latitude - (radius / 111.045)
                AND $latitude + (radius / 111.045)
                AND longitude BETWEEN $longitude - (radius / (111.045 * COS(RADIANS($latitude))))
                AND $longitude + (radius / (111.045 * COS(RADIANS($latitude))))";

            return $query->addSelect(DB::raw($select))->whereRaw($where);

        }

        return $query->addSelect(DB::raw($select));
    }
}
