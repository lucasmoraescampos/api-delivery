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
            [
                'name' => 'Plano Básico',
                'digital_menu' => '1',
                'limit_companies' => '1',
                'price' => '99.90',
                'status' => '1',
                'created_at' => '2021-01-23',
                'updated_at' => '2021-01-23'
            ],
            [
                'name' => 'Plano Intermediário',
                'digital_menu' => '1',
                'limit_companies' => '2',
                'price' => '149.90',
                'status' => '1',
                'created_at' => '2021-01-23',
                'updated_at' => '2021-01-23'
            ],
            [
                'name' => 'Plano Avançado',
                'digital_menu' => '1',
                'limit_companies' => '4',
                'price' => '249.90',
                'status' => '1',
                'created_at' => '2021-01-23',
                'updated_at' => '2021-01-23'
            ]
        ]);
    }
}
