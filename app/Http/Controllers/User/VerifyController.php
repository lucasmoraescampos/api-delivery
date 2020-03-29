<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class VerifyController extends Controller
{
    public function storeEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        if (User::where('email', $request->email)->count()) {

            return response()->json([
                'success' => false,
                'message' => 'E-mail indisponível!',
            ]);

        }

        return response()->json([
            'success' => true,
            'message' => 'E-mail disponível!',
        ]);
    }

    public function storePhone(Request $request)
    {
        $request->validate([
            'phone' => 'required'
        ]);

        if (User::where('phone', preg_replace('/[^0-9]/', '', $request->phone))->count()) {

            return response()->json([
                'success' => false,
                'message' => 'Número de telefone indisponível!',
            ]);

        }

        return response()->json([
            'success' => true,
            'message' => 'Número de telefone disponível!',
        ]);
    }
}
