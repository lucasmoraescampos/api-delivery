<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Rules\ProductRule;
use App\Rules\ProductSubcomplementsRule;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', new ProductRule()],
            'subcomplements' => ['nullable', new ProductSubcomplementsRule($request->product_id)]
        ]);

        $order = Order::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id
        ]);

        if ($request->subcomplements) {

            $order->subcomplements = $order->createSubcomplements($request->subcomplements);

        }

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Pedido realizado com sucesso!'
        ]);
    }
}
