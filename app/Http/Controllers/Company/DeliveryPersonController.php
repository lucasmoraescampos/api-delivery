<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Repositories\DeliveryPersonRepositoryInterface;
use Illuminate\Http\Request;

class DeliveryPersonController extends Controller
{
    private $deliveryPersonRepository;

    public function __construct(DeliveryPersonRepositoryInterface $deliveryPersonRepository)
    {
        $this->deliveryPersonRepository = $deliveryPersonRepository;
    }

    public function index($company_id)
    {
        $deliveryPersons = $this->deliveryPersonRepository->getByCompany($company_id);

        return response()->json([
            'success' => true,
            'data' => $deliveryPersons
        ]);
    }

    public function store(Request $request, $company_id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $deliveryPerson = $this->deliveryPersonRepository->create($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Entregador cadastrado com sucesso',
            'data' => $deliveryPerson
        ]);
    }

    public function update(Request $request, $company_id, $id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $deliveryPerson = $this->deliveryPersonRepository->update($id, $attributes);

        return response()->json([
            'success' => true,
            'message' => 'Entregador atualizado com sucesso',
            'data' => $deliveryPerson
        ]);
    }

    public function delete($company_id, $id)
    {
        $this->deliveryPersonRepository->delete($id, $company_id);

        return response()->json([
            'success' => true,
            'message' => 'Entregador excluído com sucesso'
        ]);
    }
}
