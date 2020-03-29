<?php

namespace App\Http\Controllers\Company;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\Rules\CategoryRule;
use App\Rules\PasswordRule;
use JWTAuth;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required', new CategoryRule()],
            'name' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string',
            'password' => ['required', new PasswordRule()],
            'zipcode' => 'required',
            'street_name' => 'required|string',
            'street_number' => 'required',
            'district' => 'required|string',
            'city' => 'required|string',
            'uf' => 'required|string',
        ]);

        if (Company::where('email', $request->email)->count()) {

            return response()->json([
                'success' => false,
                'message' => 'Esse email já está sendo usado por outra empresa!'
            ]);
        }

        Company::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'password' => bcrypt($request->password),
            'zipcode' => preg_replace('/[^0-9]/', '', $request->zipcode),
            'street_name' => $request->street_name,
            'street_number' => $request->street_number,
            'complement' => $request->complement,
            'district' => $request->district,
            'city' => $request->city,
            'uf' => $request->uf
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Empresa cadastrada com sucesso!'
        ]);
    }

    public function showCategories()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => $categories
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

        }
        elseif (password_verify($request->password, $company->password)) {

            return response()->json([
                'success' => true,
                'data' => [
                    'company' => $company,
                    'token' => JWTAuth::fromUser($company)
                ]
            ]);

        }
        else {

            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta!'
            ]);

        }
    }
}
