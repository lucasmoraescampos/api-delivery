<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\HttpClient;

class SmsController extends Controller
{
    public function sendConfirmationCode(Request $request)
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
}
