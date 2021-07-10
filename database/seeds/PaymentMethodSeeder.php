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
        DB::table('payment_methods')->insert([
            ['name' => 'Dinheiro',                  'icon' => 'money.png',        'allow_change_money' => true  ],
            ['name' => 'Crédito American Express',  'icon' => 'amex.png',         'allow_change_money' => false ],
            ['name' => 'Crédito Diners Club',       'icon' => 'diners.png',       'allow_change_money' => false ],
            ['name' => 'Crédito Elo',               'icon' => 'elo.png',          'allow_change_money' => false ],
            ['name' => 'Crédito Hipercard',         'icon' => 'hipercard.png',    'allow_change_money' => false ],
            ['name' => 'Crédito Mastercard',        'icon' => 'mastercard.png',   'allow_change_money' => false ],
            ['name' => 'Crédito Visa',              'icon' => 'visa.png',         'allow_change_money' => false ],
            ['name' => 'Débito American Express',   'icon' => 'amex.png',         'allow_change_money' => false ],
            ['name' => 'Débito Diners Club',        'icon' => 'diners.png',       'allow_change_money' => false ],
            ['name' => 'Débito Elo',                'icon' => 'elo.png',          'allow_change_money' => false ],
            ['name' => 'Débito Hipercard',          'icon' => 'hipercard.png',    'allow_change_money' => false ],
            ['name' => 'Débito Mastercard',         'icon' => 'mastercard.png',   'allow_change_money' => false ],
            ['name' => 'Débito Visa',               'icon' => 'visa.png',         'allow_change_money' => false ],
        ]);
    }
}
