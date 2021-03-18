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
            ['name' => 'Dinheiro',                  'icon' => $icon . '/money.png',        'allow_change_money' => true],
            ['name' => 'Crédito American Express',  'icon' => $icon . '/amex.png',         'allow_change_money' => false],
            ['name' => 'Crédito Diners Club',       'icon' => $icon . '/diners.png',       'allow_change_money' => false],
            ['name' => 'Crédito Elo',               'icon' => $icon . '/elo.png',          'allow_change_money' => false],
            ['name' => 'Crédito Hipercard',         'icon' => $icon . '/hipercard.png',    'allow_change_money' => false],
            ['name' => 'Crédito Mastercard',        'icon' => $icon . '/mastercard.png',   'allow_change_money' => false],
            ['name' => 'Crédito Visa',              'icon' => $icon . '/visa.png',         'allow_change_money' => false],
            ['name' => 'Débito American Express',   'icon' => $icon . '/amex.png',         'allow_change_money' => false],
            ['name' => 'Débito Diners Club',        'icon' => $icon . '/diners.png',       'allow_change_money' => false],
            ['name' => 'Débito Elo',                'icon' => $icon . '/elo.png',          'allow_change_money' => false],
            ['name' => 'Débito Hipercard',          'icon' => $icon . '/hipercard.png',    'allow_change_money' => false],
            ['name' => 'Débito Mastercard',         'icon' => $icon . '/mastercard.png',   'allow_change_money' => false],
            ['name' => 'Débito Visa',               'icon' => $icon . '/visa.png',         'allow_change_money' => false],
        ]);
    }
}
