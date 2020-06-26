<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $payment_methods = PaymentMethod::orderBy('name', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $payment_methods
        ]);
    }
}
