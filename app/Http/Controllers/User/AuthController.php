<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Utils\HttpClient;
use App\User;


class AuthController extends Controller
{
    public function sendRegisterCodeConfirmation(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric'
        ]);

        $code = generateCode();

        $http = new HttpClient();

        $http->setData([
            'key' => env('SMS_DEV_KEY'),
            'type' => 9,
            'number' => $request->phone,
            'msg' => urlencode('Seu codigo Meu Pedido: ' . $code)
        ]);

        $http->get('https://api.smsdev.com.br/send');

        return response()->json([
            'success' => true,
            'message' => 'Código enviado com sucesso!',
            'data' => [
                'code' => $code
            ]
        ]);
    }

    public function registerWithPhone(Request $request)
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
            'token' => JWTAuth::fromUser($user)
        ]);
    }

    public function sendLoginCodeConfirmation(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric'
        ]);

        $user = User::find(Auth::id());

        $user->sms_code = generateCode();

        $user->save();

        $http = new HttpClient();

        $http->setData([
            'key' => env('SMS_DEV_KEY'),
            'type' => 9,
            'number' => $request->phone,
            'msg' => urlencode('Seu codigo Meu Pedido: ' . $user->sms_code)
        ]);

        $http->get('https://api.smsdev.com.br/send');

        return response()->json([
            'success' => true,
            'message' => 'Código enviado com sucesso!',
            'data' => [
                'code' => $user->sms_code
            ]
        ]);
    }

    public function loginWithConfirmationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'sms_code' => 'required|string'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if ($user == null) {

            return response()->json([
                'success' => false,
                'message' => 'Este número de celular é inválido!'
            ]);

        }

        if ($user->sms_code == $request->sms_code) {

            return response()->json([
                'success' => true,
                'message' => 'Autenticado com sucesso!',
                'data' => $user,
                'token' => JWTAuth::fromUser($user)
            ]);

        }

        else {

            return response()->json([
                'success' => false,
                'message' => 'Código Inválido!',
            ]);

        }
    }

    public function loginWithFacebook(Request $request)
    {
        
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string'
        ]);

        try {

            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'Usuário desconectado com successo!'
            ]);

        }

        catch (JWTException $exception) {

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível desconectar este usuário!'
            ]);

        }

    }
}
