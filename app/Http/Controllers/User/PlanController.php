<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\PlanRepositoryInterface;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    private $planRepository;

    public function __construct(PlanRepositoryInterface $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    public function index()
    {
        $plans = $this->planRepository->getAll();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    public function store(Request $request)
    {
        $planSubscription = $this->planRepository->subscription($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Plano contratado com sucesso',
            'data' => $planSubscription
        ]);
    }
}
