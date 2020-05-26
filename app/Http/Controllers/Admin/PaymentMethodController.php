<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'icon' => 'required|file|mimes:svg'
        ]);

        $payment_method = new PaymentMethod([
            'name' => $request->name
        ]);

        $payment_method->upload($request->icon);

        $payment_method->save();

        return response()->json([
            'success' => true,
            'data' => $payment_method,
            'message' => 'Método de pagamento cadastrado com sucesso!'
        ]);
    }
}
