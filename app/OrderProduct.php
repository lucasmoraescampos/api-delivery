<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    public $timestamps = false;

    protected $table = 'orders_products';

    protected $fillable = ['order_id', 'product_id', 'qty', 'unit_price', 'note'];
}
