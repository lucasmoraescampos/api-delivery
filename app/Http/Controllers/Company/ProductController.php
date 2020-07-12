<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Rules\MenuSessionRule;
use App\Rules\ProductRule;
use App\Rules\SubcategoryRule;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::select('id', 'menu_session_id', 'photo', 'name', 'price', 'status', 'description')
            ->where('company_id', Auth::id())
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::where('id', $id)
            ->where('company_id', Auth::id())
            ->first();

        if ($product == null) {

            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado!'
            ]);

        }

        $product->complements = $product->getComplements();

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'required|string|max:2000',
            'menu_session_id' => ['required', new MenuSessionRule()],
            'subcategory_id' => ['required', new SubcategoryRule()],
            'price' => 'required|numeric',
            'is_available_sunday' => 'required|boolean',
            'is_available_monday' => 'required|boolean',
            'is_available_tuesday' => 'required|boolean',
            'is_available_wednesday' => 'required|boolean',
            'is_available_thursday' => 'required|boolean',
            'is_available_friday' => 'required|boolean',
            'is_available_saturday' => 'required|boolean',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'photo' => 'nullable|file|mimes:png,jpg,jpeg|max:8192'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'company_id' => Auth::id(),
            'menu_session_id' => $request->menu_session_id,
            'subcategory_id' => $request->subcategory_id,
            'price' => $request->price,
            'is_available_sunday' => $request->is_available_sunday,
            'is_available_monday' => $request->is_available_monday,
            'is_available_tuesday' => $request->is_available_tuesday,
            'is_available_wednesday' => $request->is_available_wednesday,
            'is_available_thursday' => $request->is_available_thursday,
            'is_available_friday' => $request->is_available_friday,
            'is_available_saturday' => $request->is_available_saturday,
            'start_time' => date('H:i', strtotime($request->start_time)),
            'end_time' => date('H:i', strtotime($request->end_time))
        ]);

        if ($request->photo != null) {
            $product->upload($request->photo);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produto cadastrado com sucesso!',
            'data' => $product
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new ProductRule(),
            'name' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:2000',
            'menu_session_id' => ['nullable', new MenuSessionRule()],
            'subcategory_id' => ['nullable', new SubcategoryRule()],
            'price' => 'nullable|numeric',
            'is_available_sunday' => 'nullable|boolean',
            'is_available_monday' => 'nullable|boolean',
            'is_available_tuesday' => 'nullable|boolean',
            'is_available_wednesday' => 'nullable|boolean',
            'is_available_thursday' => 'nullable|boolean',
            'is_available_friday' => 'nullable|boolean',
            'is_available_saturday' => 'nullable|boolean',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'status' => 'nullable|boolean',
            'photo' => 'nullable|file|max:8000|mimes:jpeg,png',
            'rebate' => 'nullable|numeric'
        ]);

        $request->start_time = $request->start_time ? date('H:i', strtotime($request->start_time)) : null;
        
        $request->end_time = $request->end_time ? date('H:i', strtotime($request->end_time)) : null;

        $data = $request->only([
            'name',
            'description',
            'menu_session_id',
            'subcategory_id',
            'price',
            'is_available_sunday',
            'is_available_monday',
            'is_available_tuesday',
            'is_available_wednesday',
            'is_available_thursday',
            'is_available_friday',
            'is_available_saturday',
            'start_time',
            'end_time',
            'status',
            'rebate'
        ]);

        $product = Product::find($id);

        if ($request->rebate !== null) {

            if ($request->rebate > 0) {

                $price = $request->price ? $request->price : $product->price;

                $result = Product::checkRebate($request->rebate, $price);

                if ($result === true) {

                    $data['promotional_price'] = $product->price - $request->rebate;

                }

                else {

                    return response()->json([
                        'success' => false,
                        'message' => $result
                    ]);

                }

            }

            else {

                $data['rebate'] = null;

                $data['promotional_price'] = null;

            }

        }

        $product->update($data);

        if ($request->photo != null) {

            $product->upload($request->photo);

        }

        $product->complements = $product->getComplements();

        return response()->json([
            'success' => true,
            'message' => 'Produto atualizado com sucesso!',
            'data' => $product
        ]);
    }
    
    public function delete($id)
    {
        $request = new Request();

        $request->request->add(['id' => new ProductRule()]);

        Product::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produto excluído com sucesso!'
        ]);
    }
}
