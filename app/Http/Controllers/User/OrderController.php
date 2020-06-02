<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Rules\CompanyRule;
use App\Rules\PaymentTypeRule;
use MercadoPago;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // $request->validate([
        //     'company_id' => ['required', new CompanyRule()],
        //     'products' => 'required|array',
        //     'payment_type' => ['required', new PaymentTypeRule()],
        //     'payment_method_id' => 'required_if:payment_type,2',
        //     'card_token' => 'required_if:payment_type,1',
        //     'card_last_number' => 'required_if:payment_type,1',
        //     'card_holder_name' => 'required_if:payment_type,1',
        //     'cashback' => 'nullable|numeric',
        //     'address' => 'required',
        //     'latitude' => 'required',
        //     'longitude' => 'required'
        // ]);

        // $errors = Order::validateProducts($request->all());

        // if ($errors !== true) {

        //     return $errors;

        // }

        // $order = Order::create([
        //     'company_id' => $request->company_id,
        //     'products' => $request->products,
        //     'payment_type' => $request->payment_type,
        //     'payment_method_id' => $request->payment_method_id,
        //     'card_token' => $request->card_token,
        //     'card_last_number' => $request->card_last_number,
        //     'card_holder_name' => $request->card_holder_name,
        //     'cashback' => $request->cashback,
        //     'address' => $request->address,
        //     'latitude' => $request->latitude,
        //     'longitude' => $request->longitude
        // ]);

        MercadoPago\SDK::setAccessToken('TEST-92940077841795-103102-7bb6a9a80fd1937790fdf595cb30226d-206536195');
        

        /*$customer = new MercadoPago\Customer();

        $customer->email = 'lucianarosamoraes@gmail.com';
        $customer->first_name = 'Luciana Rosa Moraes';
        $customer->last_name = 'Campos';
        $customer->phone = [
            'area_code' => '062',
            'number' => '984244362'
        ];
        $customer->save();*/
        

        $card = new MercadoPago\Card();
        $card->customer_id = '23540560-bUrdab7zV7Vod7';
        $card->expiration_month = 11;
        $card->expiration_year = 2025;
        $card->first_six_digits = '417006';
        $card->last_four_digits = '8020';
        $card->security_code = '123';
        $card->cardholder = [
            'Lucas Moraes Campos'
        ];
        $card->save();

        dd($card);
        
        // return response()->json([
        //     'success' => true,
        //     'data' => $payment,
        //     'message' => 'Pedido realizado com sucesso!'
        // ]);
    }
}
