<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderSubcomplement extends Model
{
    public $timestamps = false;

    protected $table = 'orders_subcomplements';

    protected $fillable = ['order_id', 'subcomplement_id', 'qty', 'unit_price'];
}
