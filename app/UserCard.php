<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    protected $table = 'users_cards';

    protected $fillable = [
        'user_id',
        'number',
        'expiration_month',
        'expiration_year',
        'security_code',
        'holder_name',
        'holder_document_type',
        'holder_document_number',
        'last_four_digits',
        'payment_method'
    ];

    protected $hidden = [
        'number',
        'security_code'
    ];

    public function setHolderDocumentType()
    {
        if (strlen($this->number) == 11) {

            $this->holder_document_type = 'CPF';

        }
        
        else {

            $this->holder_document_type = 'CNPJ';

        }
    }

    public function setLastFourDigits()
    {
        $len = strlen($this->number);
        
        $this->last_four_digits = "{$this->number[$len-4]}{$this->number[$len-3]}{$this->number[$len-2]}{$this->number[$len-1]}";
    }
}
