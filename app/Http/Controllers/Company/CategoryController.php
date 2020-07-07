<?php

namespace App\Http\Controllers\Company;

use App\Category;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
