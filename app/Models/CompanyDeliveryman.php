<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDeliveryman extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies_deliverymen';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'phone'
    ];
}
