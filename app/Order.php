<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['user_id', 'product_id'];

    public function createSubcomplements($subcomplements)
    {
        $data = [];

        foreach ($subcomplements as $subcomplement) {

            $data[] = OrderSubcomplement::create([
                'order_id' => $this->id,
                'subcomplement_id' => $subcomplement['id'], 
                'amount' => $subcomplement['amount']
            ]);

        }

        return $data;
    }
}
