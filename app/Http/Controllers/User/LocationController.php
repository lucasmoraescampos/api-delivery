<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\LocationRepositoryInterface;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    private $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function index()
    {
        $locations = $this->locationRepository->getByAuth();

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    public function store(Request $request)
    {
        $location = $this->locationRepository->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Localização cadastrada com sucesso',
            'data' => $location
        ]);
    }

    public function update(Request $request, $id)
    {
        $location = $this->locationRepository->update($id, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Localização atualizada com sucesso',
            'data' => $location
        ]);
    }

    public function delete($id)
    {
        $this->locationRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Localização excluída com sucesso'
        ]);
    }
}
