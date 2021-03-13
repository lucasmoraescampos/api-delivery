<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\CardRepositoryInterface;
use Illuminate\Http\Request;

class CardController extends Controller
{
    private $cardRepository;

    public function __construct(CardRepositoryInterface $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    public function index()
    {
        $cards = $this->cardRepository->getByAuth();

        return response()->json([
            'success' => true,
            'data' => $cards
        ]);
    }

    public function store(Request $request)
    {
        $card = $this->cardRepository->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cartão cadastrado com sucesso',
            'data' => $card
        ]);
    }
}
