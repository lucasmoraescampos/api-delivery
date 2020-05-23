<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $errors = Order::validate($request->all());

        if ($errors !== true) {

            return $errors;

        }

        $order = Order::create($request->products, $request->company_id);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Pedido realizado com sucesso!'
        ]);
    }
}
