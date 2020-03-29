<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Session;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\User;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string|unique:users'
        ]);

        if (User::where('email', $request->email)->count()) {

            return response()->json([
                'success' => false,
                'message' => 'Este e-mail já está sendo usado!'
            ]);

        }

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'phone' => preg_replace('/[^0-9]/', '', $request->phone)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cadastro realizado com sucesso!',
            'data' => $user,
            'token' => $user->generateJWT()
        ]);
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     $user = User::where('email', $request->email)->first();

    //     if ($user === null) {

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'E-mail não cadastrado!',
    //         ]);

    //     }

    //     if (Hash::check($request->password, $user->password)) {

    //         Session::init($user->id, $request->ip());

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Autenticado com sucesso',
    //             'token' => JWTAuth::fromUser($user)
    //         ]);

    //     }

    //     else {

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Senha Incorreta!',
    //         ]);

    //     }

    // }

    // public function logout(Request $request)
    // {
    //     $this->validate($request, [
    //         'token' => 'required'
    //     ]);

    //     try {

    //         JWTAuth::invalidate($request->token);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Usuário desconectado com successo'
    //         ]);

    //     }

    //     catch (JWTException $exception) {

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Usuário não pode ser desconectado'
    //         ]);

    //     }

    // }

    public function auth()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user
            ]
        ]);
    }

}
