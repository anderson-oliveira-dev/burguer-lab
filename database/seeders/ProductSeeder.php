<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tradicional = Category::where('name', 'Tradicional')->first();
        $combo = Category::where('name', 'Combo')->first();

        Product::create([
            'category_id' => $tradicional->id,
            'name' => 'Hambúrguer Clássico',
            'price' => 25.00,
            'description' => 'Pão, carne, alface, tomate, cebola e molho especial.',
            'preparation_time' => 15,
            'status' => 'disponivel',
        ]);

        Product::create([
            'category_id' => $combo->id,
            'name' => 'Combo Família',
            'price' => 45.00,
            'description' => '2 hambúrgueres + batata grande + refri 2L.',
            'preparation_time' => 25,
            'status' => 'disponivel',
        ]);
    }
}
