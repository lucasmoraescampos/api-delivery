<?php

namespace App\Http\Controllers\Company;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\Rules\CategoryRule;
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

        Company::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'password' => bcrypt($request->password)
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
                'data' => $company,
                'token' => JWTAuth::fromUser($company)
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
