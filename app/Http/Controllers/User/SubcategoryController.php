<?php

namespace App\Http\Controllers\User;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\CategoryRule;
use App\Rules\SubcategoryRule;
use App\Subcategory;

class SubcategoryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => ['required', new CategoryRule()],
        ]);

        $subcategories = Subcategory::where('category_id', $request->category_id)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $subcategories
        ]);
    }

    public function show($id)
    {
        $request = new Request();

        $request->replace(['id' => $id]);

        $request->validate([
            'id' => new SubcategoryRule()
        ]);

        $subcategory = Subcategory::find($id);

        return response()->json([
            'success' => true,
            'data' => $subcategory
        ]);
    }
}
