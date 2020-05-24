<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    public $timestamps = false;

    protected $table = 'users_locations';

    protected $fillable = [
        'user_id',
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
        'type'
    ];
}
