<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\ComplementRule;
use App\Rules\SubcomplementRule;
use App\Subcomplement;

class SubcomplementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'complement_id' => ['required', new ComplementRule()],
            'description' => 'required|string',
            'price' => 'nullable|numeric'
        ]);

        $request->price = $request->price > 0 ? $request->price : null;

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

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new SubcomplementRule(),
            'description' => 'required|string',
            'price' => 'nullable|numeric'
        ]);

        $request->price = $request->price > 0 ? $request->price : null;
        
        $subcomplement = Subcomplement::find($id);

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

    public function delete($id)
    {
        $request = new Request();

        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new SubcomplementRule()
        ]);

        Subcomplement::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item excluído com sucesso!'
        ]);
    }
}
