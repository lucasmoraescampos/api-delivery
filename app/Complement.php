<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complement extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'product_id', 'title', 'qty_min', 'qty_max', 'is_required'
    ];
}
