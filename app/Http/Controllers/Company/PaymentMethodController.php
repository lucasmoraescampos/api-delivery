<?php

namespace App\Http\Controllers\Company;

use App\CompanyPaymentMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PaymentMethod;
use App\Rules\PaymentMethodsRule;
use Illuminate\Support\Facades\Auth;

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

    public function store(Request $request)
    {
        $request->validate([
            'payment_methods' => ['required', 'array', new PaymentMethodsRule()]
        ]);

        CompanyPaymentMethod::addPaymentMethods(Auth::id(), $request->payment_methods);

        return response()->json([
            'success' => true,
            'message' => 'Métodos de pagamento cadastrados com sucesso!'
        ]);
    }
}
