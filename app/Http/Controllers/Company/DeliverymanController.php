<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Repositories\DeliverymanRepositoryInterface;
use Illuminate\Http\Request;

class DeliverymanController extends Controller
{
    private $deliverymanRepository;

    public function __construct(DeliverymanRepositoryInterface $deliverymanRepository)
    {
        $this->deliverymanRepository = $deliverymanRepository;
    }

    public function index($company_id)
    {
        $deliverymen = $this->deliverymanRepository->getByCompany($company_id);

        return response()->json([
            'success' => true,
            'data' => $deliverymen
        ]);
    }

    public function store(Request $request, $company_id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $deliveryman = $this->deliverymanRepository->create($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Entregador cadastrado',
            'data' => $deliveryman
        ]);
    }

    public function delete($company_id, $id)
    {
        $this->deliverymanRepository->delete($id, $company_id);

        return response()->json([
            'success' => true,
            'message' => 'Entregador excluído'
        ]);
    }
}
