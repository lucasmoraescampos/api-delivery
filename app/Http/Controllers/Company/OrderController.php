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
            ->select('o.id', 'o.latitude', 'o.longitude', 'o.delivery_time', 'o.amount', 'o.status', 'u.name', 'u.surname')
            ->leftJoin('users as u', 'u.id', 'o.user_id')
            ->where('company_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status');
        
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
