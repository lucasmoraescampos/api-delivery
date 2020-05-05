<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complement extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'product_id', 'title', 'limit', 'is_required'
    ];
}
