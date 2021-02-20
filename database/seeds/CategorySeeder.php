<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = env('APP_URL') . '/assets/images';

        DB::table('categories')->insert([
            ['name' => 'Restaurantes', 'icon' => $path . '/'],
            ['name' => 'Supermercados', 'icon' => $path . '/'],
            ['name' => 'Padarias', 'icon' => $path . '/'],
            ['name' => 'Bebidas', 'icon' => $path . '/'],
            ['name' => 'Farmácias', 'icon' => $path . '/'],
            ['name' => 'Água e gás', 'icon' => $path . '/']
        ]);
    }
}
