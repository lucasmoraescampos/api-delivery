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
            ['name' => 'Dinheiro',                  'icon' => $icon . '/money.svg',        'allow_change_money' => true,  'mercadopago_id' => null],
            ['name' => 'Crédito American Express',  'icon' => $icon . '/amex.svg',         'allow_change_money' => false, 'mercadopago_id' => 'amex'],
            ['name' => 'Crédito Diners Club',       'icon' => $icon . '/diners.svg',       'allow_change_money' => false, 'mercadopago_id' => null],
            ['name' => 'Crédito Elo',               'icon' => $icon . '/elo.svg',          'allow_change_money' => false, 'mercadopago_id' => 'elo'],
            ['name' => 'Crédito Hipercard',         'icon' => $icon . '/hipercard.svg',    'allow_change_money' => false, 'mercadopago_id' => 'hipercard'],
            ['name' => 'Crédito Mastercard',        'icon' => $icon . '/mastercard.svg',   'allow_change_money' => false, 'mercadopago_id' => 'master'],
            ['name' => 'Crédito Visa',              'icon' => $icon . '/visa.svg',         'allow_change_money' => false, 'mercadopago_id' => 'visa'],
            ['name' => 'Débito American Express',   'icon' => $icon . '/amex.svg',         'allow_change_money' => false, 'mercadopago_id' => null],
            ['name' => 'Débito Diners Club',        'icon' => $icon . '/diners.svg',       'allow_change_money' => false, 'mercadopago_id' => null],
            ['name' => 'Débito Elo',                'icon' => $icon . '/elo.svg',          'allow_change_money' => false, 'mercadopago_id' => null],
            ['name' => 'Débito Hipercard',          'icon' => $icon . '/hipercard.svg',    'allow_change_money' => false, 'mercadopago_id' => null],
            ['name' => 'Débito Mastercard',         'icon' => $icon . '/mastercard.svg',   'allow_change_money' => false, 'mercadopago_id' => null],
            ['name' => 'Débito Visa',               'icon' => $icon . '/visa.svg',         'allow_change_money' => false, 'mercadopago_id' => null]
        ]);
    }
}
