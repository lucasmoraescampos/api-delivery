<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\Rules\CategoryRule;
use App\Rules\CompanyAuthRule;
use App\Rules\PaymentMethodsRule;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'category_id' => ['required', new CategoryRule()],
            'name' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string',
            'password' => 'required'
        ]);

        if (Company::where('email', $request->email)->count()) {

            return response()->json([
                'success' => false,
                'message' => 'Esse e-mail já está sendo usado por outra empresa!'
            ]);

        }

        $company = Company::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Empresa cadastrada com sucesso!',
            'data' => $company,
            'token' => JWTAuth::fromUser($company)
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new CompanyAuthRule(),
            'category_id' => ['nullable', new CategoryRule()],
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:9',
            'street_name' => 'nullable|string|max:255',
            'street_number' => 'nullable|string|max:10',
            'complement' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:200',
            'uf' => 'nullable|string|max:2',
            'latitude' => 'nullable|string|max:40',
            'longitude' => 'nullable|string|max:40',
            'photo' => 'nullable|file|max:8000|mimes:jpeg,png',
            'min_value' => 'nullable|numeric',
            'delivery_price' => 'nullable|numeric',
            'waiting_time' => 'nullable|numeric',
            'is_open' => 'nullable|boolean',
            'accept_outsourced_delivery' => 'nullable|boolean',
            'accept_withdrawal_local' => 'nullable|boolean',
            'accept_payment_app' => 'nullable|boolean',
            'accept_payment_delivery' => 'nullable|boolean',
            'payment_methods' => ['required_if:accept_payment_delivery,1', 'array', new PaymentMethodsRule()]
        ]);

        $data = $request->only([
            'category_id',
            'name',
            'phone',
            'password',
            'zipcode',
            'street_name',
            'street_number',
            'complement',
            'district',
            'city',
            'uf',
            'latitude',
            'longitude',
            'min_value',
            'delivery_price',
            'waiting_time',
            'is_open',
            'accept_outsourced_delivery',
            'accept_withdrawal_local',
            'accept_payment_app',
            'accept_payment_delivery'
        ]);

        $company = Company::find($id);

        $company->update($data);

        if ($request->payment_methods) {

            $company->setPaymentMethods($request->payment_methods);

        }

        if ($request->photo) {

            $company->upload($request->photo);

        }

        return response()->json([
            'success' => true,
            'data' => $company,
            'message' => 'Empresa atualizada com sucesso!'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $company = Company::where('email', $request->email)->first();

        if ($company == null) {

            return response()->json([
                'success' => false,
                'message' => 'Este e-mail não está cadastrado!'
            ]);
            
        }
        
        elseif (password_verify($request->password, $company->password)) {

            $company->payment_methods = $company->getPaymentMethods();

            return response()->json([
                'success' => true,
                'data' => $company,
                'token' => JWTAuth::fromUser($company)
            ]);

        }
        
        else {

            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta!'
            ]);

        }
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string'
        ]);

        try {

            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'Empresa desconectada com successo!'
            ]);

        }

        catch (JWTException $exception) {

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível fazer logout!'
            ]);

        }
    }

    public function performance()
    {
        $company = Company::find(Auth::id());

        $performance = $company->getPerformance();

        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }
}
