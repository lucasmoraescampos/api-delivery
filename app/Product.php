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

    public function uploadPhoto($photo)
    {
        $name = uniqid(date('HisYmd'));

        $ext = substr($photo, 11, strpos($photo, ';') - 11);

        $full_name = "{$name}.{$ext}";

        // $photo = str_replace('data:image/png;base64,', '', $photo);

        // $photo = str_replace(' ', '+', $photo);

        $this->photo = "https://api.meupedido.org/storage/products/$full_name";

        $this->save();

        // Storage::put("products/$full_name", base64_decode($photo));
    }

    public function insertComplement($complement)
    {
        $created = Complement::create([
            'product_id' => $this->id,
            'title' => $complement['title'],
            'qty_min' => $complement['qty_min'],
            'qty_max' => $complement['qty_max']
        ]);

        foreach ($complement['subcomplements'] as $subcomplement) {

            Subcomplement::create([
                'complement_id' => $created->id,
                'description' => $subcomplement['description'],
                'price' => $subcomplement['price']
            ]);
        }
    }

    public function insertComplements($complements)
    {
        foreach ($complements as $complement) {

            $created = Complement::create([
                'product_id' => $this->id,
                'title' => $complement['title'],
                'qty_min' => $complement['qty_min'],
                'qty_max' => $complement['qty_max']
            ]);

            foreach ($complement['subcomplements'] as $subcomplement) {

                Subcomplement::create([
                    'complement_id' => $created->id,
                    'description' => $subcomplement['description'],
                    'price' => $subcomplement['price']
                ]);
            }
        }
    }

    public function getPhotoPath()
    {
        return route('productPhoto', ['photo' => $this->photo]);
    }

    public function getComplements()
    {
        $complements = Complement::where('product_id', $this->id)
            ->orderBy('title', 'asc')
            ->get();

        foreach ($complements as &$complement) {
            $complement->subcomplements = Subcomplement::where('complement_id', $complement->id)->get();
        }

        unset($complement);

        return $complements;
    }

    public static function exist($id)
    {
        return Product::where('id', $id)->count() > 0;
    }

    public static function getComplementsByProduct($id)
    {
        return Complement::select('complements.*')
            ->leftJoin('products', 'products.id', 'complements.product_id')
            ->where('products.company_id', Auth::id())
            ->where('products.id', $id)
            ->get();
    }
}
