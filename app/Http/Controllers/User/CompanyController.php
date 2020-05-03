<?php

namespace App\Http\Controllers\User;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        $company = Company::select('name', 'waiting_time')
            ->where('id', $id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $company
        ]);
    }
}
