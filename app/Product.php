<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
    }

    public function getComplements()
    {
        $complements = Complement::where('product_id', $this->id)->get();

        foreach ($complements as &$complement) {
            $complement->subcomplements = Subcomplement::where('complement_id', $complement->id)->get();
        }

        return $complements;
    }

    public static function getByCompany($company_id)
    {
        $products = Product::from('products as p')
            ->select('m.name as menu_session_name', 'p.id', 'p.menu_session_id', 'p.photo', 'p.name', 'p.description', 'p.price', 'p.promotional_price')
            ->leftJoin('menu_sessions as m', 'm.id', 'p.menu_session_id')
            ->where('p.company_id', $company_id)
            ->where(Product::today(), AVAILABLE)
            ->get()
            ->groupBy('menu_session_id');

        $data = [];

        foreach ($products as $values) {

            $data[] = [
                'menu_session_id' => $values[0]->menu_session_id,
                'menu_session_name' => $values[0]->menu_session_name,
                'products' => $values
            ];

        }

        return $data;
    }

    public static function getBySubcategory($subcategory_id)
    {
        return Product::from('products as p')
            ->select('p.id', 'p.photo', 'p.name', 'p.description', 'p.price', 'p.promotional_price', 'c.id as company_id', 'c.photo as company_photo', 'c.waiting_time', 'c.delivery_price', 'c.latitude', 'c.longitude')
            ->leftJoin('companies as c', 'c.id', 'p.company_id')
            ->where('p.subcategory_id', $subcategory_id)
            ->where('c.is_open', OPEN)
            ->where('p.status', ACTIVE)
            ->where(Product::today(), AVAILABLE)
            ->get();
    }

    public static function getBySearch($search)
    {
        return Product::from('products as p')
            ->select('p.id', 'p.photo', 'p.name', 'p.description', 'p.price', 'p.promotional_price', 'p.created_at', 'c.id as company_id', 'c.photo as company_photo', 'c.waiting_time', 'c.delivery_price', 'c.latitude', 'c.longitude', 'c.is_open')
            ->leftJoin('companies as c', 'c.id', 'p.company_id')
            ->where('p.name', 'like', "%$search%")
            ->orderBy('p.created_at', 'asc')
            ->orderBy('c.is_open', 'desc')
            ->distinct()
            ->get();
    }

    private static function today()
    {

        switch (date('N')) {

            case 7:
                return 'p.is_available_sunday';

            case 1:
                return 'p.is_available_monday';

            case 2:
                return 'p.is_available_tuesday';

            case 3:
                return 'p.is_available_wednesday';

            case 4:
                return 'p.is_available_thursday';

            case 5:
                return 'p.is_available_friday';

            case 6:
                return 'p.is_available_saturday';

        }

    }
}
