<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\FavoriteRepositoryInterface;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    private $favoriteRepository;

    public function __construct(FavoriteRepositoryInterface $favoriteRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
    }

    public function store(Request $request)
    {
        $favorite = $this->favoriteRepository->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empresa favoritada com sucesso',
            'data'    => $favorite
        ]);
    }
}
