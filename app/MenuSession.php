<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuSession extends Model
{
    protected $table = 'menu_sessions';

    public $timestamps = false;

    protected $fillable = [
        'company_id', 'name', 'position'
    ];

    public function isLinked()
    {
        return Product::where('menu_session_id', $this->id)->count() > 0;
    }

    public function createPosition()
    {
        $this->position = Product::where('company_id', $this->company_id)->count() + 1;
    }
}
