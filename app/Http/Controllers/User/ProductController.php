<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Rules\CompanyRule;
use App\Rules\ProductRule;
use App\Rules\SubcategoryRule;
use App\Subcomplement;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'company_id' => ['required_without:subcategory_id', new CompanyRule()],
            'subcategory_id' => ['required_without:company_id', new SubcategoryRule()],
        ]);

        if ($request->company_id) {

            $data = Product::getAvailableByCompany($request->company_id);

        }

        else {

            $data = Product::getAvailableBySubcategory($request->subcategory_id);

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
