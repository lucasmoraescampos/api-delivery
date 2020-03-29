<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuSession extends Model
{
    protected $table = 'menu_sessions';

    public $timestamps = false;

    protected $fillable = [
        'company_id', 'name'
    ];

    public function isLinked()
    {
        return Product::where('menu_session_id', $this->id)->count() > 0;
    }
}
