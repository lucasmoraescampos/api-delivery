<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Rules\CompanyRule;
use App\Rules\PaymentTypeRule;
use App\Rules\OrderRule;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
    
    public function show($id)
    {
        $request = new Request();

        $request->request->add(['id' => $id]);

        $request->validate(['id' => new OrderRule()]);

        $order = Order::getById($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => ['required', new CompanyRule()],
            'products' => 'required|array',
            'payment_type' => ['required', new PaymentTypeRule()],
            'payment_method_id' => 'required',
            'card_token' => 'required_if:payment_type,1',
            'card_number' => 'required_if:payment_type,1',
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
            'card_number' => $request->card_number,
            'card_holder_name' => $request->card_holder_name,
            'cashback' => $request->cashback,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        if ($order === false) {

            return response()->json([
                'success' => false,
                'message' => 'Pagamento Recusado. Tente outro método de pagamento!'
            ]);

        }
        
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Pedido realizado com sucesso!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new OrderRule(),
            'feedback' => 'required_without:delivered|numeric|min:1|max:5',
            'delivered' => 'required_without:feedback|bool'
        ]);
        
        $order = Order::find($id);

        if ($request->feedback) {

            if ($order->feedback != null) {

                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido já foi avaliado!'
                ]);

            }

            $order->sendFeedback($request->feedback);

        }

        elseif ($request->delivered) {

            if ($order->status == COMPLETED) {

                return response()->json([
                    'success' => false,
                    'message' => 'Entrega já confirmada!'
                ]);

            }

            $order->completeOrder();

        }

        return response()->json([
            'success' => true,
            'message' => 'Atualização realizada com sucesso!',
            'data' => $order
        ]);
    }
}
