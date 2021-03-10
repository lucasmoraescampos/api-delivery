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
        $image = env('IMAGES_URL') . '/categories';

        DB::table('categories')->insert([
            ['name' => 'Restaurantes',              'image' => $image],
            ['name' => 'Supermercados',             'image' => $image],
            ['name' => 'Padarias',                  'image' => $image],
            ['name' => 'Bebidas',                   'image' => $image],
            ['name' => 'Farmácias',                 'image' => $image],
            ['name' => 'Água e gás',                'image' => $image],
            ['name' => 'Modas',                     'image' => $image],
            ['name' => 'Materiais de construção',   'image' => $image],
            ['name' => 'Pets Shop',                 'image' => $image]
        ]);
    }
}
