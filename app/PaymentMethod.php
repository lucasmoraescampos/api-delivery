<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'icon'];

    public function upload($file)
    {
        $name = uniqid(date('HisYmd'));

        $ext = $file->extension();

        $full_name = "{$name}.{$ext}";

        $file->storeAs('payment_methods', $full_name);

        $this->icon = 'https://api.meupedido.org/storage/payment_methods/' . $full_name;
    }
}
