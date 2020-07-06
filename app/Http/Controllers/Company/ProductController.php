<?php

namespace App\Http\Controllers\Company;

use App\Complement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Rules\AvailableShiftRule;
use App\Rules\ComplementRule;
use App\Rules\MenuSessionRule;
use App\Rules\ProductRule;
use App\Rules\SubcategoryRule;
use App\Subcomplement;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::select('id', 'menu_session_id', 'photo', 'name', 'description')
            ->where('company_id', Auth::id())
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
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
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
            'start_time' => $request->start_time,
            'end_time' => $request->end_time
        ]);

        if ($request->photo != null) {
            $product->uploadPhoto($request->photo);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produto cadastrado com sucesso!',
            'data' => $product
        ]);
    }

    public function storeComplement(Request $request)
    {
        $request->validate([
            'product_id' => ['required', new ProductRule()],
            'title' => 'required|string',
            'qty_min' => 'required_if:is_required,1',
            'qty_max' => 'required',
            'is_required' => 'required'
        ]);

        $complement = Complement::create([
            'product_id' => $request->product_id,
            'title' => $request->title,
            'qty_min' => $request->qty_min,
            'qty_max' => $request->qty_max,
            'is_required' => $request->is_required
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Complemento cadastrado com sucesso!',
            'data' => $complement
        ]);
    }

    public function storeSubcomplement(Request $request)
    {
        $request->validate([
            'complement_id' => ['required', new ComplementRule()],
            'description' => 'required|string',
            'price' => 'nullable|numeric'
        ]);

        $subcomplement = Subcomplement::create([
            'complement_id' => $request->complement_id,
            'description' => $request->description,
            'price' => $request->price
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item cadastrado com sucesso!',
            'data' => $subcomplement
        ]);
    }

    public function storePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|file|mimes:png,jpg,jpeg|max:8192',
            'product_id' => 'required'
        ]);

        $product = Product::find($request->product_id);

        if ($product == null) {

            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado!'
            ]);
        }

        $product->uploadPhoto($request->photo);

        return response()->json([
            'success' => true,
            'message' => 'Foto atualizada com sucesso!'
        ]);
    }

    public function update(Request $request, $id)
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
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i'
        ]);

        $product = Product::where('id', $id)
            ->where('company_id', Auth::id())
            ->first();

        if ($product == null) {

            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado!'
            ]);

        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
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
            'start_time' => $request->start_time,
            'end_time' => $request->end_time
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produto atualizado com sucesso!',
            'data' => $product
        ]);
    }

    public function updateComplement(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'qty_min' => 'required_if:is_required,1',
            'qty_max' => 'required',
            'is_required' => 'required'
        ]);

        $complement = Complement::select('complements.*')
            ->where('complements.id', $id)
            ->leftJoin('products', 'products.id', 'complements.product_id')
            ->where('products.company_id', Auth::id())
            ->first();

        if ($complement == null) {

            return response()->json([
                'success' => false,
                'message' => 'Complemento não encontrado!'
            ]);

        }

        $complement->update([
            'title' => $request->title,
            'qty_min' => $request->qty_min,
            'qty_max' => $request->qty_max,
            'is_required' => $request->is_required
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Complemento atualizado com sucesso!',
            'data' => $complement
        ]);
    }

    public function updateSubcomplement(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
            'price' => 'nullable|numeric'
        ]);

        $subcomplement = Subcomplement::select('subcomplements.*')
            ->where('subcomplements.id', $id)
            ->leftJoin('complements', 'complements.id', 'subcomplements.complement_id')
            ->leftJoin('products', 'products.id', 'complements.product_id')
            ->where('products.company_id', Auth::id())
            ->first();

        if ($subcomplement == null) {

            return response()->json([
                'success' => false,
                'message' => 'Item não encontrado!'
            ]);

        }

        $subcomplement->update([
            'description' => $request->description,
            'price' => $request->price
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item atualizado com sucesso!',
            'data' => $subcomplement
        ]);
    }

    public function updatePromotion(Request $request, $product_id)
    {
        $request->validate([
            'value' => 'nullable|numeric'
        ]);

        $product = Product::where('id', $product_id)
            ->where('company_id', Auth::id())
            ->first();

        if ($product == null) {

            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado!'
            ]);
            
        }

        if ($request->value != null) {

            if ($request->value < 2) {

                return response()->json([
                    'success' => false,
                    'message' => 'O desconto não pode ser menor que R$ 2,00.'
                ]);

            }

            if (percentValue($product->price, $request->value) < 10) {

                return response()->json([
                    'success' => false,
                    'message' => 'O desconto não pode ser menor que 10% do valor do produto.'
                ]);

            }

            $product->promotional_price = $product->price - $request->value;

        }

        else {

            $product->promotional_price = null;

        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Desconto aplicado com sucesso!'
        ]);
    }

    public function updateStatus($id)
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

        $product->status = $product->status == 1 ? 0 : 1;
        
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!'
        ]);
    }

    public function delete($id)
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

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produto excluído com sucesso!'
        ]);
    }

    public function deleteComplement($id)
    {
        $complement = Complement::select('complements.*')
            ->where('complements.id', $id)
            ->leftJoin('products', 'products.id', 'complements.product_id')
            ->where('products.company_id', Auth::id())
            ->first();

        if ($complement == null) {

            return response()->json([
                'success' => false,
                'message' => 'Complemento não encontrado!'
            ]);
        }

        $complement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complemento excluído com sucesso!'
        ]);
    }

    public function deleteSubcomplement($id)
    {
        $subcomplement = Subcomplement::select('subcomplements.*')
            ->where('subcomplements.id', $id)
            ->leftJoin('complements', 'complements.id', 'subcomplements.complement_id')
            ->leftJoin('products', 'products.id', 'complements.product_id')
            ->where('products.company_id', Auth::id())
            ->first();

        if ($subcomplement == null) {

            return response()->json([
                'success' => false,
                'message' => 'Item não encontrado!'
            ]);

        }

        $subcomplement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item excluído com sucesso!'
        ]);
    }
}
