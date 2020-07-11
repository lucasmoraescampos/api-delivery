<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\VoucherCodeRule;
use App\Rules\VoucherRule;
use App\Voucher;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::where('company_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vouchers
        ]);
    }

    public function show($id)
    {
        $request = new Request();

        $request->request->add(['id' => $id]);

        $request->validade(['id' => new VoucherRule()]);

        $voucher = Voucher::find($id);

        return response()->json([
            'success' => true,
            'data' => $voucher
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', new VoucherCodeRule()],
            'qty' => 'nullable|max:50000',
            'value' => 'required',
            'min_value' => 'nullable|min:0',
            'expiration_date' => 'nullable|date'
        ]);

        $request->code = strtoupper($request->code);

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

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new VoucherRule(),
            'code' => ['required', new VoucherCodeRule()],
            'qty' => 'nullable|max:50000',
            'value' => 'required',
            'min_value' => 'nullable|min:0',
            'expiration_date' => 'nullable|date'
        ]);

        $request->code = strtoupper($request->code);

        $voucher = Voucher::find($request->id);

        if ($voucher->code != $request->code && !Voucher::checkCode($request->code)) {

            return response()->json([
                'success' => false,
                'message' => 'Este código já está sendo usado em outro voucher!'
            ]);

        }

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

        else {

            $user_id = $voucher->id;

        }

        $voucher->update([
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
            'message' => 'Voucher atualizado com sucesso!'
        ]);
    }

    public function delete($id)
    {
        $request = new Request();

        $request->request->add(['id' => $id]);

        $request->validate(['id' => new VoucherRule()]);

        Voucher::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Voucher excluído com sucesso!'
        ]);
    }
}
