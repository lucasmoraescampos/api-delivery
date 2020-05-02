<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
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
            ->whereIn('id', function ($query) {

                $query->select('subcategory_id')
                ->from(with(new Product())->getTable())
                ->distinct();

            })
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
