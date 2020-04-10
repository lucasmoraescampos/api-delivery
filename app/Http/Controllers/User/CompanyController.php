<?php

namespace App\Http\Controllers\User;

use App\Category;
use App\Company;
use App\Complement;
use App\Subcategory;
use App\MenuSession;
use App\Product;
use App\Http\Controllers\Controller;
use App\Subcomplement;

class CompanyController extends Controller
{
    public function index($category_id, $subcategory_id = null)
    {
        if ($subcategory_id == null) {

            $companies = Company::select('id', 'photo', 'name', 'waiting_time', 'latitude', 'longitude', 'delivery_price', 'is_open')
                ->where('category_id', $category_id)
                ->orderBy('created_at', 'asc')
                ->orderBy('is_open', 'desc')
                ->get();
        } else {

            $companies = Company::select('id', 'photo', 'name', 'waiting_time', 'latitude', 'longitude', 'delivery_price', 'is_open')
                ->where('category_id', $category_id)
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

        return response()->json([
            'success' => true,
            'data' => $companies
        ]);
    }

    public function showCategories()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function showSubcategory($id)
    {
        $subcategory = Subcategory::find($id);

        return response()->json([
            'success' => true,
            'data' => $subcategory
        ]);
    }

    public function showSubcategories($category_id)
    {
        $subcategories = Subcategory::where('category_id', $category_id)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $subcategories
        ]);
    }

    public function showProducts($company_id)
    {
        $products = Product::from('products as p')
            ->select('m.name as menu_session_name', 'p.menu_session_id', 'p.photo', 'p.name', 'p.description', 'p.price', 'p.promotional_price')
            ->leftJoin('menu_sessions as m', 'm.id', 'p.menu_session_id')
            ->where('p.company_id', $company_id)
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

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function showProductsBySubcategory($category_id, $subcategory_id)
    {
        $products = Product::select('products.photo', 'products.name', 'products.description', 'products.price', 'products.promotional_price', 'companies.photo as company_photo', 'companies.waiting_time', 'companies.delivery_price', 'companies.latitude', 'companies.longitude')
            ->leftJoin('companies', 'companies.id', 'products.company_id')
            ->where('companies.category_id', $category_id)
            ->where('products.subcategory_id', $subcategory_id)
            ->where('companies.is_open', OPEN)
            ->where('products.status', ACTIVE);

        switch (date('N')) {

            case 7:
                $products = $products->where('products.is_available_sunday', ACTIVE);
                break;

            case 1:
                $products = $products->where('products.is_available_monday', ACTIVE);
                break;

            case 2:
                $products = $products->where('products.is_available_tuesday', ACTIVE);
                break;

            case 3:
                $products = $products->where('products.is_available_wednesday', ACTIVE);
                break;

            case 4:
                $products = $products->where('products.is_available_thursday', ACTIVE);
                break;

            case 5:
                $products = $products->where('products.is_available_friday', ACTIVE);
                break;

            case 6:
                $products = $products->where('products.is_available_saturday', ACTIVE);
                break;
        }

        $products = $products->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function showProduct($id)
    {
        $product = Product::select('photo', 'name', 'description', 'price', 'promotional_price')
            ->where('id', $id)
            ->first();

        $complements = Subcomplement::from('subcomplements as s')
            ->select('c.id', 'c.title', 'c.qty_min', 'c.qty_max', 's.description', 's.price')
            ->leftJoin('complements as c', 'c.id', 's.complement_id')
            ->where('c.product_id', $id)
            ->get();

        $product->complements = [];

        foreach ($complements as $complement) {

            $product->complements[] = [
                'id' => $complement->id,
                'title' => $complement->title,
                'subcomplements' => [
                    'description' => $complement->description,
                    'price' => $complement->price
                ]
            ];
            
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
}
