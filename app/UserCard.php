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
        'payment_method'
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
}
