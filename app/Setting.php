<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'qty_max_menu_session'
    ];

    public static function getQtyMaxMenuSession()
    {
        return Setting::first()->qty_max_menu_session;
    }
}
