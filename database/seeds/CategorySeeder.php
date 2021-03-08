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
            ['name' => 'Restaurantes', 'image' => $path . '/'],
            ['name' => 'Supermercados', 'image' => $path . '/'],
            ['name' => 'Padarias', 'image' => $path . '/'],
            ['name' => 'Bebidas', 'image' => $path . '/'],
            ['name' => 'Farmácias', 'image' => $path . '/'],
            ['name' => 'Água e gás', 'image' => $path . '/'],
            ['name' => 'Lojas', 'image' => $path . '/'],
        ]);
    }
}
