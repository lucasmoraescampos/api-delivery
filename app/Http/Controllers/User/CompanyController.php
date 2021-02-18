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
}
