<?php

namespace App\Http\Controllers\Company;

use App\Company;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\OrderRule;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $company = Company::find(Auth::id());

        $orders = $company->getOrders();
        
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function show($id)
    {
        $request = new Request();

        $request->request->add(['id' => $id]);

        $request->validate(['id' => new OrderRule]);

        $company = Company::find(Auth::id());

        $order = $company->getOrderById($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new OrderRule(),
            'status' => 'required|min:1|max:5'
        ]);

        $order = Order::find($id);
        
        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Pedido atualizado com sucesso!'
        ]);
    }
}