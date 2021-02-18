<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function signUp(Request $request)
    {
        $user = $this->userRepository->create($request->all());

        $token = $this->userRepository->createAccessToken($user);

        return response()->json([
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'data' => $user,
            'token' => $token
        ]);
    }

    public function authenticate(Request $request)
    {
        $user = $this->userRepository->authenticate($request->all());

        $token = $user ? $this->userRepository->createAccessToken($user) : null;

        return response()->json([
            'success' => true,
            'message' => 'Autenticação realizada com sucesso',
            'data' => $user,
            'token' => $token
        ]);
    }

    public function authenticateWithProvider(Request $request)
    {
        $user = $this->userRepository->authenticateWithProvider($request->all());

        $token = $this->userRepository->createAccessToken($user);

        return response()->json([
            'success' => true,
            'message' => 'Autenticação realizada com sucesso',
            'data' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $this->userRepository->invalidAccessToken($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Sessão encerrada com sucesso'
        ]);
    }
}
