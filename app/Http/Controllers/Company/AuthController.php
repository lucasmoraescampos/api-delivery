<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\Rules\CategoryRule;
use App\Rules\CompanyAuthRule;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required', new CategoryRule()],
            'name' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string',
            'password' => 'required'
        ]);

        if (Company::where('email', $request->email)->count()) {

            return response()->json([
                'success' => false,
                'message' => 'Esse e-mail já está sendo usado por outra empresa!'
            ]);
        }

        $company = Company::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Empresa cadastrada com sucesso!',
            'data' => $company,
            'token' => JWTAuth::fromUser($company)
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new CompanyAuthRule(),
            'category_id' => ['nullable', new CategoryRule()],
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:9',
            'street_name' => 'nullable|string|max:255',
            'street_number' => 'nullable|string|max:10',
            'complement' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:200',
            'uf' => 'nullable|string|max:2',
            'latitude' => 'nullable|string|max:40',
            'longitude' => 'nullable|string|max:40',
            'photo' => 'nullable|file|size:8000|mimes:jpeg,png',
            'min_value' => 'nullable|numeric',
            'delivery_price' => 'nullable|numeric',
            'waiting_time' => 'nullable|numeric',
            'is_open' => 'nullable|boolean',
            'accept_payment_app' => 'nullable|boolean'
        ]);

        $data = $request->only([
            'category_id',
            'name',
            'phone',
            'password',
            'zipcode',
            'street_name',
            'street_number',
            'complement',
            'district',
            'city',
            'uf',
            'latitude',
            'longitude',
            'min_value',
            'delivery_price',
            'waiting_time',
            'is_open',
            'accept_payment_app'
        ]);

        $company = Company::find($id);

        $company->update($data);

        if ($request->photo) {

            $company->upload($request->photo);

        }

        return response()->json([
            'success' => true,
            'message' => 'Empresa atualizada com sucesso!'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $company = Company::where('email', $request->email)->first();

        if ($company == null) {

            return response()->json([
                'success' => false,
                'message' => 'Empresa não encontrada!'
            ]);
        } elseif (password_verify($request->password, $company->password)) {

            return response()->json([
                'success' => true,
                'data' => $company,
                'token' => JWTAuth::fromUser($company)
            ]);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta!'
            ]);
        }
    }
}
