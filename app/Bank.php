<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'code', 'name', 'status'
    ];

    protected $attributes = [
        'status' => ACTIVE
    ];
}
