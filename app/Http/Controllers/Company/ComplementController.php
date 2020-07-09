<?php

namespace App\Http\Controllers\Company;

use App\Complement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\ComplementRule;
use App\Rules\ProductRule;

class ComplementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', new ProductRule()],
            'title' => 'required|string',
            'qty_min' => 'nullable|required_if:is_required,1|min:1',
            'qty_max' => 'required|min:1',
            'is_required' => 'required|boolean'
        ]);

        $request->qty_min = $request->is_required ? $request->qty_min : null;

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

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new ComplementRule(),
            'title' => 'required|string',
            'qty_min' => 'nullable|required_if:is_required,1|min:1',
            'qty_max' => 'required|min:1',
            'is_required' => 'required|boolean'
        ]);

        $request->qty_min = $request->is_required ? $request->qty_min : null;

        $complement = Complement::find($id);

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

    public function delete($id)
    {
        $request = new Request();

        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new ComplementRule()
        ]);

        Complement::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complemento excluído com sucesso!'
        ]);
    }
}
