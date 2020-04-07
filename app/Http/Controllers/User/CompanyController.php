<?php

namespace App\Http\Controllers\User;

use App\Category;
use App\Company;
use App\Subcategory;
use App\MenuSession;
use App\Product;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index($category_id, $subcategory_id = null)
    {
        if ($subcategory_id == null) {

            $companies = Company::select('id', 'photo', 'name', 'waiting_time', 'latitude', 'longitude', 'delivery_price', 'is_open')
                ->where('category_id', $category_id)
                ->orderBy('created_at', 'asc')
                ->get();

        }
        
        else {

            $companies = Company::select('id', 'photo', 'name', 'waiting_time', 'latitude', 'longitude', 'delivery_price', 'is_open')
                ->whereIn('id', function ($query) use ($subcategory_id) {

                    $query->select('company_id')
                        ->from(with(new Product)->getTable())
                        ->where('subcategory_id', $subcategory_id)
                        ->distinct();
                        
                })->get();
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

    public function showMenuSessions($company_id)
    {
        $menu_sessions = MenuSession::where('company_id', $company_id)->get();

        return response()->json([
            'success' => true,
            'data' => $menu_sessions
        ]);
    }

    public function showProducts($company_id)
    {
        $products = Product::select('menu_sessions.name as menu_session', 'products.name', 'products.description', 'products.price', 'products.promotional_price')
            ->leftJoin('menu_sessions', 'menu_sessions.id', 'products.menu_session_id')
            ->where('products.company_id', $company_id)
            ->get()
            ->groupBy('menu_session');

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function showProduct($id)
    {
    }
}
