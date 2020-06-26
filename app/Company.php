<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Storage;

class Company extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'companies';

    protected $fillable = [
        'category_id', 'name', 'email', 'phone', 'password'
    ];

    protected $attributes = [
        'accept_payment_app' => 0,
        'status' => WAITING,
        'is_open' => 0
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getQtyMenuSessions()
    {
        return MenuSession::where('company_id', $this->id)->count();
    }

    public static function getByCategory($category_id)
    {
        return Company::select('id', 'photo', 'name', 'waiting_time', 'latitude', 'longitude', 'delivery_price', 'is_open', 'feedback')
            ->where('category_id', $category_id)
            ->whereIn('id', function ($query) {

                $query->select('company_id')
                    ->from(with(new Product)->getTable())
                    ->distinct();
                    
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('is_open', 'desc')
            ->get();
    }

    public static function getBySubcategory($subcategory_id)
    {
        return Company::select('id', 'photo', 'name', 'waiting_time', 'latitude', 'longitude', 'delivery_price', 'is_open', 'feedback')
            ->whereIn('id', function ($query) use ($subcategory_id) {

                $query->select('company_id')
                    ->from(with(new Product)->getTable())
                    ->where('subcategory_id', $subcategory_id)
                    ->distinct();
                    
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('is_open', 'desc')
            ->get();
    }

    public static function getBySearch($search)
    {
        return Product::from('products as p')
            ->select('c.id', 'c.photo', 'c.name', 'c.waiting_time', 'c.latitude', 'c.longitude', 'c.delivery_price', 'c.created_at', 'c.is_open', 'c.feedback')
            ->leftJoin('companies as c', 'c.id', 'p.company_id')
            ->where('p.name', 'like', "%$search%")
            ->orWhere('c.name', 'like', "%$search%")
            ->orderBy('c.created_at', 'asc')
            ->orderBy('c.is_open', 'desc')
            ->distinct()
            ->get();
    }

    public function upload($file)
    {
        $this->deleteLastPhoto();
        
        $name = uniqid(date('HisYmd'));

        $ext = $file->extension();

        $full_name = "{$name}.{$ext}";

        $file->storeAs('companies', $full_name);

        $this->photo = 'https://api.meupedido.org/storage/companies/' . $full_name;

        $this->save();
    }

    private function deleteLastPhoto() {

        if ($this->photo) {

            $array = explode('/', $this->photo);

            $photo = 'companies/' . end($array);

            Storage::delete($photo);

        }

    }
}
