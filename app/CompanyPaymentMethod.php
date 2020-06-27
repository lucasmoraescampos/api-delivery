<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyPaymentMethod extends Model
{
    public $timestamps = false;
    
    protected $table = 'companies_payment_methods';

    protected $fillable = [
        'company_id', 'payment_method_id'
    ];

    public static function addPaymentMethods($company_id, $payment_methods)
    {
        foreach ($payment_methods as $pm) {

            CompanyPaymentMethod::create([
                'company_id' => $company_id,
                'payment_method_id' => $pm 
            ]);

        }
    }
}
