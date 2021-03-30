<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * Constants.
     */
    const PAYMENT_ONLINE = 1;

    const PAYMENT_DELIVERY = 2;

    const STATUS_WAITING_CONFIRMATION = 0;

    const STATUS_PREPARING = 1;

    const STATUS_WAITING_DELIVERY = 2;

    const STATUS_DELIVIRING = 3;

    const STATUS_FINISHED = 4;

    const STATUS_CANCELED = 5;

    const TYPE_LOCAL = 0;

    const TYPE_DELIVERY = 1;

    const TYPE_WITHDRAWAL = 2;

    const DELIVERY_TYPE_COMPANY = 1;

    const DELIVERY_TYPE_OUTSOURCED = 2;

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'company_id',
        'user_id',
        'company_deliveryman_id',
        'mercadopago_id',
        'number',
        'price',
        'total_price',
        'delivery_price',
        'fee',
        'online_payment_fee',
        'type',
        'payment_type',
        'change_money',
        'payment_method',
        'products',
        'delivery_location',
        'delivery_forecast',
        'delivery_type',
        'status'
    ];

    /**
	 * The model's default values for attributes.
	 *
	 * @var array
	 */
	protected $attributes = [
        'status' =>  self::STATUS_WAITING_CONFIRMATION
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
        'delivery_forecast' => 'datetime',
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
        return $this->belongsTo('App\Models\Company');
    }

    /**
     * Get the company that owns the order.
     */
    public function company_deliveryman()
    {
        return $this->belongsTo('App\Models\CompanyDeliveryman');
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
