<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->insert([

            // Restaurantes
            [
                'category_id' => 1,
                'name' => 'Plano Básico',
                'fee' => 8,
                'online_payment_fee' => 4.5,
                'delivery_type' => 1,
                'status' => true
            ],
            [
                'category_id' => 1,
                'name' => 'Plano Entrega',
                'fee' => 10,
                'online_payment_fee' => 4.5,
                'delivery_type' => 2,
                'status' => true
            ],

            // Supermercados
            [
                'category_id' => 2,
                'name' => 'Plano Básico',
                'fee' => 6,
                'online_payment_fee' => 4.5,
                'delivery_type' => 1,
                'status' => true
            ],

            // Padarias
            [
                'category_id' => 3,
                'name' => 'Plano Básico',
                'fee' => 8,
                'online_payment_fee' => 4.5,
                'delivery_type' => 1,
                'status' => true
            ],
            [
                'category_id' => 3,
                'name' => 'Plano Entrega',
                'fee' => 10,
                'online_payment_fee' => 4.5,
                'delivery_type' => 2,
                'status' => true
            ],

            // Bebidas
            [
                'category_id' => 4,
                'name' => 'Plano Básico',
                'fee' => 6,
                'online_payment_fee' => 4.5,
                'delivery_type' => 1,
                'status' => true
            ],

            // Farmácias
            [
                'category_id' => 5,
                'name' => 'Plano Básico',
                'fee' => 8,
                'online_payment_fee' => 4.5,
                'delivery_type' => 1,
                'status' => true
            ],
            [
                'category_id' => 5,
                'name' => 'Plano Entrega',
                'fee' => 10,
                'online_payment_fee' => 4.5,
                'delivery_type' => 2,
                'status' => true
            ],

            // Água e gás
            [
                'category_id' => 6,
                'name' => 'Plano Básico',
                'fee' => 8,
                'online_payment_fee' => 4.5,
                'delivery_type' => 1,
                'status' => true
            ],

            // Modas
            [
                'category_id' => 7,
                'name' => 'Plano Básico',
                'fee' => 8,
                'online_payment_fee' => 4.5,
                'delivery_type' => 1,
                'status' => true
            ],
            [
                'category_id' => 7,
                'name' => 'Plano Entrega',
                'fee' => 10,
                'online_payment_fee' => 4.5,
                'delivery_type' => 2,
                'status' => true
            ],

            // Materiais de construção
            [
                'category_id' => 8,
                'name' => 'Plano Básico',
                'fee' => 8,
                'online_payment_fee' => 4.5,
                'delivery_type' => 1,
                'status' => true
            ],

            // Pets shop
            [
                'category_id' => 9,
                'name' => 'Plano Básico',
                'fee' => 8,
                'online_payment_fee' => 4.5,
                'delivery_type' => 1,
                'status' => true
            ],

        ]);
    }
}
