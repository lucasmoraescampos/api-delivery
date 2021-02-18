<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index($company_id)
    {
        $order = $this->orderRepository->getByCompany($company_id);

        return response()->json([
            'success' => true,
            'message' => 'Pedido realizado com sucesso',
            'data' => $order
        ]);
    }

    public function store(Request $request, $company_id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $order = $this->orderRepository->createByCompany($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Pedido realizado com sucesso',
            'data' => $order
        ]);
    }
}
