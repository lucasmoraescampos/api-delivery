<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Subcomplement extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'complement_id', 'description', 'price'
    ];
}
