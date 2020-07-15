<?php

namespace App\Http\Controllers\Company;

use App\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $banks
        ]);
    }
}
