<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Subcategory;
use Illuminate\Support\Facades\Auth;

class SubcategoryController extends Controller
{
    public function index()
    {
        $company = Auth::user();

        $categories = Subcategory::where('category_id', $company->category_id)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
