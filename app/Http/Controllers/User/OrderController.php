<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Rules\CompanyRule;
use App\Rules\PaymentTypeRule;
use MercadoPago\SDK as MercadoPagoSDK;
use MercadoPago\Payment as MercadoPagoPayment;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => ['required', new CompanyRule()],
            'products' => 'required|array',
            'payment_type' => ['required', new PaymentTypeRule()],
            'payment_method_id' => 'required_if:payment_type,2',
            'card_token' => 'required_if:payment_type,1',
            'card_last_number' => 'required_if:payment_type,1',
            'card_holder_name' => 'required_if:payment_type,1',
            'cashback' => 'nullable|numeric',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        $errors = Order::validateProducts($request->all());

        if ($errors !== true) {

            return $errors;

        }

        $order = Order::create([
            'company_id' => $request->company_id,
            'products' => $request->products,
            'payment_type' => $request->payment_type,
            'payment_method_id' => $request->payment_method_id,
            'card_token' => $request->card_token,
            'card_last_number' => $request->card_last_number,
            'card_holder_name' => $request->card_holder_name,
            'cashback' => $request->cashback,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        MercadoPagoSDK::setAccessToken('TEST-92940077841795-103102-7bb6a9a80fd1937790fdf595cb30226d-206536195');
        
        $payment = new MercadoPagoPayment();
        $payment->transaction_amount = 191;
        $payment->token = $request->card_token;
        $payment->description = 'Meu Pedido';
        $payment->installments = 1;
        $payment->payment_method_id = $request->payment_method_id;
        $payment->payer = ['email' => 'lukaspgtu@hotmail.com'];

        $payment->save();
        
        return response()->json([
            'success' => true,
            'data' => $payment,
            'message' => 'Pedido realizado com sucesso!'
        ]);
    }
}
