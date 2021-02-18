<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = env('APP_URL') . '/assets/images';

        DB::table('payment_methods')->insert([
            ['name' => 'Dinheiro', 'icon' => $path . '/money.svg'],
            ['name' => 'Crédito American Express', 'icon' => $path . '/amex.svg'],
            ['name' => 'Crédito Diners Club', 'icon' => $path . '/diners.svg'],
            ['name' => 'Crédito Elo', 'icon' => $path . '/elo.svg'],
            ['name' => 'Crédito Hipercard', 'icon' => $path . '/hipercard.svg'],
            ['name' => 'Crédito Mastercard', 'icon' => $path . '/mastercard.svg'],
            ['name' => 'Crédito Visa', 'icon' => $path . '/visa.svg'],
            ['name' => 'Débito American Express', 'icon' => $path . '/amex.svg'],
            ['name' => 'Débito Diners Club', 'icon' => $path . '/diners.svg'],
            ['name' => 'Débito Elo', 'icon' => $path . '/elo.svg'],
            ['name' => 'Débito Hipercard', 'icon' => $path . '/hipercard.svg'],
            ['name' => 'Débito Mastercard', 'icon' => $path . '/mastercard.svg'],
            ['name' => 'Débito Visa', 'icon' => $path . '/visa.svg']
        ]);
    }
}
