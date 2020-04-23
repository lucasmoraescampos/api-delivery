<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'company_id',
        'menu_session_id',
        'subcategory_id',
        'name',
        'description',
        'price',
        'is_available_sunday',
        'is_available_monday',
        'is_available_tuesday',
        'is_available_wednesday',
        'is_available_thursday',
        'is_available_friday',
        'is_available_saturday',
        'start_time',
        'end_time'
    ];

    protected $attributes = [
        'status' => 1
    ];

    public function uploadPhoto($file)
    {
        $name = uniqid(date('HisYmd'));

        $ext = $file->extension();

        $full_name = "{$name}.{$ext}";

        $file->storeAs('products', $full_name);

        $this->photo = 'https://api.meupedido.org/storage/products/' . $full_name;

        $this->save();
    }

    public function getComplements()
    {
        $complements = Complement::where('product_id', $this->id)
            ->orderBy('title', 'asc')
            ->get();

        foreach ($complements as &$complement) {
            $complement->subcomplements = Subcomplement::where('complement_id', $complement->id)->get();
        }

        return $complements;
    }
}
