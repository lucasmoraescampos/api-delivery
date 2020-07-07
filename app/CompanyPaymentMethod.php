<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CompanyPaymentMethod extends Model
{
    public $timestamps = false;
    
    protected $table = 'companies_payment_methods';

    protected $fillable = [
        'company_id', 'payment_method_id'
    ];

    public static function addPaymentMethods($payment_methods)
    {
        foreach ($payment_methods as $pm) {

            CompanyPaymentMethod::create([
                'company_id' => Auth::id(),
                'payment_method_id' => $pm 
            ]);

        }
    }
}
