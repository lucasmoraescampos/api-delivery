<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategories';

    public $timestamps = false;

    protected $fillable = [
        'category_id', 'name'
    ];
}
