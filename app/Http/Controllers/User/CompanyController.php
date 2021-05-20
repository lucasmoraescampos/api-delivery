<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepositoryInterface;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    private $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function store(Request $request)
    {
        $company = $this->companyRepository->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empresa cadastrada com sucesso',
            'data' => $company
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = $this->companyRepository->update($id, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empresa atualizada com sucesso',
            'data' => $company
        ]);
    }

    public function delete($id)
    {
        $this->companyRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Empresa excluída com sucesso'
        ]);
    }

    public function favorite(Request $request)
    {
        $this->companyRepository->favorite($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empresa favoritada com sucesso'
        ]);
    }
}
