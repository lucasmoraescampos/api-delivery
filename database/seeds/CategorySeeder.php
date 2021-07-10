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
        DB::table('categories')->insert([
            ['name' => 'Restaurante',               'image' => 'restaurants.png',   'slug' =>  'restaurants'    ],
            ['name' => 'Supermercado',              'image' => 'supermarkets.png',  'slug' =>  'supermarkets'   ],
            ['name' => 'Padaria',                   'image' => 'bakeries.png',      'slug' =>  'bakeries'       ],
            ['name' => 'Bebidas',                   'image' => 'drinks.png',        'slug' =>  'drinks'         ],
            ['name' => 'Farmácia',                  'image' => 'pharmacies.png',    'slug' =>  'pharmacies'     ],
            ['name' => 'Água e gás',                'image' => 'others.png',        'slug' =>  'water-and-gas'  ],
            ['name' => 'Modas',                     'image' => 'fashions.png',      'slug' =>  'fashions'       ],
            ['name' => 'Materiais de construção',   'image' => 'construction.png',  'slug' =>  'construction'   ],
            ['name' => 'Pet Shop',                  'image' => 'petshops.png',      'slug' =>  'petshops'       ]
        ]);
    }
}
