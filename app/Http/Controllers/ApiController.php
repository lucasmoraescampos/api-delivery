<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Exceptions\CustomException;
use App\Mail\SendVerificationCode;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Segment;
use App\Repositories\HttpClientRepository;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApiController extends Controller
{
    public function categories()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function plans($category_id)
    {
        $plans = Plan::where('category_id', $category_id)
            ->where('status', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    public function company($slug)
    {
        $company = Company::with(['payment_methods'])->where('slug', $slug)->first();

        $company = collect($company->toArray())->except([
            'document_number', 'balance', 'status', 'created_at', 'updated_at'
        ]);

        if (!$company) {
            throw new CustomException('Nenhuma empresa encontrada', 200);
        }

        $menu = Segment::with(['products:id,segment_id,name,description,price,rebate,image', 'products.complements.subcomplements'])
            ->orderBy('position', 'asc')
            ->where('company_id', $company['id'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'company' => $company,
                'menu' => $menu
            ]
        ]);

    }

    public function product($id)
    {
        $product = Product::select('id', 'segment_id', 'name', 'description', 'price', 'rebate', 'image')
            ->with(['complements.subcomplements'])
            ->where('id', $id)
            ->first();

        if (!$product) {
            throw new CustomException('Nenhum produto encontrado', 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function companiesByAllCategories(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $categories = Category::with(['companies' => function ($query) use ($request) {

            $query = $query->select('category_id', 'slug', 'image', 'name', 'evaluation', 'waiting_time', 'delivery_price', 'open')
                ->distance($request->latitude, $request->longitude)
                ->where('status', Company::STATUS_ACTIVE)
                ->where('open', true)
                ->inRandomOrder()
                ->orderBy('distance', 'asc')
                ->limit(10);           

        }])->get();

        $data = [];

        foreach ($categories as $category) {
            if ($category->companies->count() > 0) {
                $data[] = $category;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function companiesByCategory(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'category_slug' => 'required|string',
            'limit' => 'nullable|numeric|min:1',
            'offset' => 'nullable|numeric|min:1'
        ]);

        $category = Category::where('slug', $request->category_slug)->first();

        if (!$category) {
            throw new CustomException('Category not found', 404);
        }

        $query = Company::select('category_id', 'slug', 'image', 'name', 'evaluation', 'waiting_time', 'delivery_price', 'open')
            ->distance($request->latitude, $request->longitude)
            ->where('status', Company::STATUS_ACTIVE)
            ->where('category_id', $category->id)
            ->orderBy('open', 'desc')
            ->orderBy('distance', 'asc');

        if ($request->limit) {
            $query = $query->limit($request->limit);
        }

        if ($request->offset) {
            $query = $query->offset($request->limit);
        }

        $companies = $query->get();

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'companies' => $companies
            ]
        ]);
    }

    public function paymentMethods()
    {
        $data = PaymentMethod::all();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function checkDuplicity(Request $request)
    {
        $request->validate([
            'email' => 'required_without_all:phone,slug',
            'phone' => 'required_without_all:email,slug',
            'slug' => 'required_without_all:email,phone',
        ]);

        if ($request->email) {

            $success = User::where('email', $request->email)->count() == 0;

            $message = $success ? 'OK' : 'Este e-mail já está sendo usado.';

        }

        elseif ($request->phone) {

            $success = User::where('phone', $request->phone)->count() == 0;

            $message = $success ? 'OK' : 'Este número de celular já está sendo usado.';
            
        }

        elseif ($request->slug) {

            $request->slug = strtolower($request->slug);

            if (preg_match('/^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/', $request->slug) == false) {
                throw new CustomException('Slug inválido.', 422);
            }

            $success = Company::where('slug', $request->slug)->count() == 0;

            $message = $success ? 'OK' : 'Este slug já está sendo usado.';
            
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }

    public function sendCodeVerification(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|string|email|max:255',
            'phone' => 'required_without:email|string|max:11'
        ]);

        $code = generateCode();

        if ($request->email) {

            VerificationCode::where('email', $request->email)->delete();

            VerificationCode::create(['email' => $request->email, 'code' => $code]);

            Mail::to($request->email)->send(new SendVerificationCode($code));

        }

        else {

            VerificationCode::where('phone', $request->phone)->delete();

            VerificationCode::create(['phone' => $request->phone, 'code' => $code]);

            $httpClient = new HttpClientRepository();

            $httpClient->setData([
                'key' => env('SMS_DEV_KEY'),
                'type' => 9,
                'number' => $request->phone,
                'msg' => "Seu codigo Meu Pedido: $code"
            ]);

            $httpClient->post('https://api.smsdev.com.br/v1/send');

        }

        return response()->json([
            'success' => true,
            'message' => 'Código de verificação enviado com sucesso'
        ]);
    }

    public function confirmCodeVerification(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|string|email|max:255',
            'phone' => 'required_without:email|string|max:11',
            'code' => 'required|string|min:5|max:5'
        ]);

        if ($request->email) {

            $verificationCodeQuery = VerificationCode::where('email', $request->email);

            $verification = $verificationCodeQuery->orderBy('id', 'desc')->first();

            if (!$verification) {
                throw new CustomException('Nenhum código de verificação enviado para este e-mail', 200);
            }

        }

        else {

            $verificationCodeQuery = VerificationCode::where('phone', $request->phone);

            $verification = $verificationCodeQuery->orderBy('id', 'desc')->first();

            if (!$verification->code) {
                throw new CustomException('Nenhum código de verificação enviado para este número de celular', 200);
            }

        }

        if ($verification->code != $request->code) {
            throw new CustomException('Código invalido', 200);
        }

        $verificationCodeQuery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Código de verificação confirmado com sucesso'
        ]);
    }
}
