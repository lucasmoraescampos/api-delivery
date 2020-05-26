<?php

namespace App\Http\Controllers\User;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PaymentMethod;
use App\Rules\CategoryRule;
use App\Rules\CompanyRule;
use App\Rules\SubcategoryRule;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => ['required_without:subcategory_id', new CategoryRule()],
            'subcategory_id' => ['required_without:category_id', new SubcategoryRule()],
        ]);

        if ($request->category_id) {

            $companies = Company::getByCategory($request->category_id);

        } else {

            $companies = Company::getBySubcategory($request->subcategory_id); 

        }

        return response()->json([
            'success' => true,
            'data' => $companies
        ]);
    }

    public function show($id)
    {
        $request = new Request();

        $request->replace(['id' => $id]);

        $request->validate(['id' => new CompanyRule()]);

        $company = Company::select('name', 'min_value', 'delivery_price', 'waiting_time', 'accept_payment_app')
            ->where('id', $id)
            ->first();

        $company->payment_methods = PaymentMethod::all();

        return response()->json([
            'success' => true,
            'data' => $company
        ]);
    }
}
