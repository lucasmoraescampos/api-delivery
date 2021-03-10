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
        $icon = env('IMAGES_URL') . '/payment-methods';

        DB::table('payment_methods')->insert([
            ['name' => 'Dinheiro',                  'icon' => $icon . '/money.svg'],
            ['name' => 'Crédito American Express',  'icon' => $icon . '/amex.svg'],
            ['name' => 'Crédito Diners Club',       'icon' => $icon . '/diners.svg'],
            ['name' => 'Crédito Elo',               'icon' => $icon . '/elo.svg'],
            ['name' => 'Crédito Hipercard',         'icon' => $icon . '/hipercard.svg'],
            ['name' => 'Crédito Mastercard',        'icon' => $icon . '/mastercard.svg'],
            ['name' => 'Crédito Visa',              'icon' => $icon . '/visa.svg'],
            ['name' => 'Débito American Express',   'icon' => $icon . '/amex.svg'],
            ['name' => 'Débito Diners Club',        'icon' => $icon . '/diners.svg'],
            ['name' => 'Débito Elo',                'icon' => $icon . '/elo.svg'],
            ['name' => 'Débito Hipercard',          'icon' => $icon . '/hipercard.svg'],
            ['name' => 'Débito Mastercard',         'icon' => $icon . '/mastercard.svg'],
            ['name' => 'Débito Visa',               'icon' => $icon . '/visa.svg']
        ]);
    }
}
