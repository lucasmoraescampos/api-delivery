<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\UserLocationRule;
use App\UserLocation;
use Illuminate\Support\Facades\Auth;

class UserLocationController extends Controller
{
    public function index()
    {
        $locations = UserLocation::where('user_id', Auth::id())->get();

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    public function show($id)
    {
        $request = new Request();

        $request->replace(['id' => $id]);

        $request->validate(['id' => new UserLocationRule()]);

        $location = UserLocation::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $location
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'street_name' => 'required|string|max:255',
            'street_number' => 'required|max:20',
            'complement' => 'nullable|string|max:255',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'uf' => 'required|string|max:2',
            'postal_code' => 'required|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'required|max:40',
            'longitude' => 'required|max:40'
        ]);

        $location = UserLocation::create([
            'user_id' => Auth::id(),
            'street_name' => $request->street_name,
            'street_number' => $request->street_number,
            'complement' => $request->complement,
            'district' => $request->district,
            'city' => $request->city,
            'uf' => $request->uf,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'type' => $request->type
        ]);

        return response()->json([
            'success' => true,
            'data' => $location,
            'message' => 'Localização cadastrada com sucesso!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new UserLocationRule(),
            'street_name' => 'required|string|max:255',
            'street_number' => 'required|max:20',
            'complement' => 'nullable|string|max:255',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'uf' => 'required|string|max:2',
            'postal_code' => 'required|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'required|max:40',
            'longitude' => 'required|max:40'
        ]);

        $location = UserLocation::where('id', $id)->first();

        $location->update([
            'street_name' => $request->street_name,
            'street_number' => $request->street_number,
            'complement' => $request->complement,
            'district' => $request->district,
            'city' => $request->city,
            'uf' => $request->uf,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'type' => $request->type
        ]);

        return response()->json([
            'success' => true,
            'data' => $location,
            'message' => 'Localização atualizada com sucesso!'
        ]);
    }

    public function delete($id)
    {
        $request = new Request();

        $request->request->add(['id' => $id]);

        $request->validate(['id' => new UserLocationRule()]);

        UserLocation::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Localização excluída com sucesso!'
        ]);
    }
}
