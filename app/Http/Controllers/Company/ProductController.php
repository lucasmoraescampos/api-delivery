<?php

namespace App\Http\Controllers\Company;

use App\Complement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MenuSession;
use App\Product;
use App\Rules\AvailableShiftRule;
use App\Rules\MenuSessionRule;
use App\Rules\SubcategoryRule;
use App\Subcomplement;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::select('id', 'menu_session_id', 'name', 'description')
            ->where('company_id', Auth::id())
            ->orderBy('created_at', 'asc')
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

        $product->photo = $product->getPhotoPath();

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function showPhoto($photo)
    {
        $path = storage_path("app/public/products/$photo");

        if (file_exists($path)) {

            $array = explode('.', $path);

            $type = end($array);

            return response()->file($path, ['Content-Type' => "image/$type"]);
        }

        abort(404);
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
            'available_shift' => ['required', new AvailableShiftRule()],
            'promotional_price' => 'nullable|numeric'
        ]);

        if ($request->promotional_price) {

            if ($request->promotional_price < 2) {

                return response()->json([
                    'success' => false,
                    'message' => 'Valor promocional não pode ser menor que R$ 2,00.'
                ], 422);
            }

            if (percentValue($request->price, $request->promotional_price) < 10) {

                return response()->json([
                    'success' => false,
                    'message' => 'Valor promocional não pode ser abaixo de 10% do valor do produto.'
                ], 422);
            }

            else {
                $request->promotional_price = $request->price - $request->promotional_price;
            }
        }

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
            'available_shift' => $request->available_shift,
            'promotional_price' => $request->promotional_price
        ]);

        if ($request->complements != null && $request->complements != []) {
            $product->insertComplements($request->complements);
        }

        $menu_session = MenuSession::where('id', $product->menu_session_id)->first();

        $product->menu_session = $menu_session->name;

        return response()->json([
            'success' => true,
            'message' => 'Produto cadastrado com sucesso!',
            'data' => $product
        ]);
    }

    public function storeComplement(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'title' => 'required|string',
            'qty_min' => 'required',
            'qty_max' => 'required'
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('company_id', Auth::id())
            ->first();

        if ($product == null) {

            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado!'
            ]);
        }

        $product->insertComplement([
            'title' => $request->title,
            'qty_min' => $request->qty_min,
            'qty_max' => $request->qty_max,
            'subcomplements' => $request->subcomplements
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Complemento cadastrado com sucesso!'
        ]);
    }

    public function storeSubcomplement(Request $request)
    {
        $request->validate([
            'complement_id' => 'required',
            'description' => 'required|string',
            'price' => 'required'
        ]);

        $complement = Complement::where('complements.id', $request->complement_id)
            ->leftJoin('products', 'products.id', 'complements.product_id')
            ->where('products.company_id', Auth::id())
            ->first();

        if ($complement == null) {

            return response()->json([
                'success' => false,
                'message' => 'Complemento não encontrado!'
            ]);
        }

        $complement->insertSubcomplement([
            'complement_id' => $request->complement_id,
            'description' => $request->description,
            'price' => $request->price
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item cadastrado com sucesso!'
        ]);
    }

    public function storePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|file|mimes:png,jpg,jpeg',
            'product_id' => 'required'
        ]);

        $product = Product::find($request->product_id);

        if ($product == null) {

            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado!'
            ]);
        }

        $product->upload($request->photo);

        $product->save();

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
            'available_shift' => ['required', new AvailableShiftRule()],
            'promotional_price' => 'numeric'
        ]);

        if ($request->promotional_price) {

            if ($request->promotional_price < 2) {

                return response()->json([
                    'success' => false,
                    'message' => 'Valor promocional não pode ser menor que R$ 2,00.'
                ], 422);
            }

            if (percentValue($request->price, $request->promotional_price) < 10) {

                return response()->json([
                    'success' => false,
                    'message' => 'Valor promocional não pode ser abaixo de 10% do valor do produto.'
                ], 422);
            }

            else {
                $request->promotional_price = $request->price - $request->promotional_price;
            }
        }

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
            'available_shift' => $request->available_shift,
            'promotional_price' => $request->promotional_price
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produto atualizado com sucesso!'
        ]);
    }

    public function updateComplement(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'qty_min' => 'required',
            'qty_max' => 'required'
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
            'qty_max' => $request->qty_max
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Complemento atualizado com sucesso!'
        ]);
    }

    public function updateSubcomplement(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
            'price' => 'required|numeric'
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
            'message' => 'Item atualizado com sucesso!'
        ]);
    }

    public function updateStatus(Request $request, $id)
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
