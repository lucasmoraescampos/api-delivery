<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index($company_id)
    {
        $products = $this->productRepository->getByCompany($company_id);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function store(Request $request, $company_id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $product = $this->productRepository->create($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Produto cadastrado com sucesso',
            'data' => $product
        ]);
    }

    public function update(Request $request, $company_id, $id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $product = $this->productRepository->update($id, $attributes);

        return response()->json([
            'success' => true,
            'message' => 'Produto atualizado com sucesso',
            'data' => $product
        ]);
    }

    public function delete($company_id, $id)
    {
        $this->productRepository->delete($id, $company_id);

        return response()->json([
            'success' => true,
            'message' => 'Produto excluído com sucesso'
        ]);
    }
}
