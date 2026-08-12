<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Tradicional', 'description' => 'Hambúrgueres tradicionais'],
            ['name' => 'Combo', 'description' => 'Combos com batata e bebida'],
            ['name' => 'Bebida', 'description' => 'Bebidas diversas'],
            ['name' => 'Acompanhamento', 'description' => 'Porções e adicionais'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                ['description' => $cat['description']]
            );
        }
    }
}
