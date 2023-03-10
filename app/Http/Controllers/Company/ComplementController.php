<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Repositories\ComplementRepositoryInterface;
use Illuminate\Http\Request;

class ComplementController extends Controller
{
    private $complementRepository;

    public function __construct(ComplementRepositoryInterface $complementRepository)
    {
        $this->complementRepository = $complementRepository;
    }

    public function store(Request $request, $company_id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $complement = $this->complementRepository->create($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Complemento cadastrado com sucesso',
            'data' => $complement
        ]);
    }

    public function update(Request $request, $company_id, $id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $complement = $this->complementRepository->update($id, $attributes);

        return response()->json([
            'success' => true,
            'message' => 'Complemento atualizado com sucesso',
            'data' => $complement
        ]);
    }

    public function delete($company_id, $id)
    {
        $this->complementRepository->delete($id, $company_id);

        return response()->json([
            'success' => true,
            'message' => 'Complemento excluído com sucesso'
        ]);
    }
}
