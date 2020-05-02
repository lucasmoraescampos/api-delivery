<?php

namespace App\Http\Controllers\User;

use App\Category;
use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereIn('id', function ($query) {

            $query->select('category_id')
                ->from(with(new Company())->getTable())
                ->distinct();
                
        })->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
