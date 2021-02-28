<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Repositories\SubcomplementRepositoryInterface;
use Illuminate\Http\Request;

class SubcomplementController extends Controller
{
    private $subcomplementRepository;

    public function __construct(SubcomplementRepositoryInterface $subcomplementRepository)
    {
        $this->subcomplementRepository = $subcomplementRepository;
    }

    public function store(Request $request, $company_id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $subcomplement = $this->subcomplementRepository->create($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Subcomplemento cadastrado com sucesso',
            'data' => $subcomplement
        ]);
    }

    public function update(Request $request, $company_id, $id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $subcomplement = $this->subcomplementRepository->update($id, $attributes);

        return response()->json([
            'success' => true,
            'message' => 'Subcomplemento atualizado com sucesso',
            'data' => $subcomplement
        ]);
    }

    public function delete($company_id, $id)
    {
        $this->subcomplementRepository->delete($id, $company_id);

        return response()->json([
            'success' => true,
            'message' => 'Subcomplemento excluído com sucesso'
        ]);
    }
}
