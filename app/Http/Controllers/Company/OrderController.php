<?php

namespace App\Http\Controllers\Company;

use App\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::from('orders as o')
            ->select('o.id', 'o.latitude', 'o.longitude', 'o.delivery_forecast', 'o.amount', 'o.status', 'o.created_at', 'o.delivered_at', 'u.name', 'u.surname')
            ->leftJoin('users as u', 'u.id', 'o.user_id')
            ->where('o.company_id', Auth::id())
            ->orderBy('o.created_at', 'desc')
            ->get()
            ->groupBy('status');
        
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
