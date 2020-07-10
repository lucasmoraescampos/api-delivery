<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Voucher;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'qty' => 'nullable|max:50000',
            'value' => 'required',
            'min_value' => 'nullable|min:0',
            'expiration_date' => 'nullable|date_format:"Y-m-d H:i:s"'
        ]);

        if (!Voucher::checkCode($request->code)) {

            return response()->json([
                'success' => false,
                'message' => 'Este código já está sendo usado em outro voucher!'
            ]);

        }

        $user_id = null;

        if ($request->user) {

            $user = Voucher::findUser($request->user);

            if ($user == null) {

                return response()->json([
                    'success' => false,
                    'message' => 'O usuário informado não foi encontrado!'
                ]);

            }

            $user_id = $user->id;

        }

        $voucher = Voucher::create([
            'company_id' => Auth::id(),
            'user_id' => $user_id,
            'code' => $request->code,
            'qty' => $request->qty,
            'value' => $request->value,
            'min_value' => $request->min_value,
            'expiration_date' => $request->expiration_date
        ]);

        return response()->json([
            'success' => true,
            'data' => $voucher,
            'message' => 'Voucher cadastrado com sucesso!'
        ]);

    }
}
