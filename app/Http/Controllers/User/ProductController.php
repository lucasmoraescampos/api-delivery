<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Rules\CompanyRule;
use App\Rules\ProductRule;
use App\Rules\SubcategoryRule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'required_without_all:company_id,subcategory_id',
            'company_id' => ['required_without_all:search,subcategory_id', new CompanyRule()],
            'subcategory_id' => ['required_without_all:search,company_id', new SubcategoryRule()],
        ]);

        if ($request->search) {

            $data = Product::getBySearch($request->search);

        }

        elseif ($request->company_id) {

            $data = Product::getByCompany($request->company_id);

        }

        else {

            $data = Product::getBySubcategory($request->subcategory_id);

        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $request = new Request();

        $request->replace(['id' => $id]);

        $request->validate([
            'id' => new ProductRule()
        ]);

        $product = Product::select('id', 'photo', 'name', 'description', 'price', 'promotional_price')
            ->where('id', $id)
            ->first();

        $product->complements = $product->getComplements();

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
}
