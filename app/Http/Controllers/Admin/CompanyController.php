<?php

namespace App\Http\Controllers\Admin;

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

    public function update(Request $request, $id)
    {
        $company = $this->companyRepository->update($id, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empresa atualizada com sucesso',
            'data' => $company
        ]);
    }
}
