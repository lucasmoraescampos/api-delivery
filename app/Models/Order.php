<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * Constants.
     */
    const PAYMENT_LOCAL = 0;

    const PAYMENT_ONLINE = 1;

    const PAYMENT_DELIVERY = 2;

    const STATUS_WAITING = 0;

    const TYPE_LOCAL = 0;

    const TYPE_DELIVERY = 1;

    const TYPE_WITHDRAWAL = 2;

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'company_id',
        'customer_id',
        'table_id',
        'attendant_id',
        'delivery_person_id',
        'price',
        'total_price',
        'delivery_price',
        'change_money',
        'payment_type',
        'products',
        'payment_method',
        'delivery_location',
        'delivery_forecast',
        'type'
    ];

    /**
	 * The model's default values for attributes.
	 *
	 * @var array
	 */
	protected $attributes = [
        'status' =>  self::STATUS_WAITING
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'total_price' => 'float',
        'delivery_price' => 'float',
        'change_money' => 'float',
        'products' => Json::class,
        'payment_method' => Json::class,
        'delivery_location' => Json::class,
        'evaluation' => 'integer',
        'type' => 'integer',
        'status' => 'integer',
        'evaluation' => 'integer'
    ];

    /**
     * Get the company that owns the order.
     */
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
