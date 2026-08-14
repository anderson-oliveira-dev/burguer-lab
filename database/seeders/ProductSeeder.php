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
        $tradicional    = Category::where('name', 'Tradicional')->first();
        $bebidas        = Category::where('name', 'Bebida')->first();
        $combo          = Category::where('name', 'Combo')->first();
        $acompanhamento = Category::where('name', 'Acompanhamento')->first();

        Product::create([
            'category_id'      => $tradicional->id,
            'name'             => 'X-Burguer',
            'price'            => 25.00,
            'description'      => 'Pão, carne 150g, alface, tomate, cebola e molho especial.',
            'preparation_time' => 15,
            'status'           => 'disponivel',
            'image'            => 'x-burguer.jpeg',
        ]);

        Product::create([
            'category_id'      => $tradicional->id,
            'name'             => 'X-Frango Duplo',
            'price'            => 30.00,
            'description'      => 'Pão, blend de frango duplo 180g, queijo cheddar e molho barbecue.',
            'preparation_time' => 18,
            'status'           => 'disponivel',
            'image'            => 'x-frango.jpeg',
        ]);

        Product::create([
            'category_id'      => $tradicional->id,
            'name'             => 'X-Bacon',
            'price'            => 28.00,
            'description'      => 'Pão integral, bacon crocante, carne 150g, alface, tomate, cebola e molho especial.',
            'preparation_time' => 20,
            'status'           => 'disponivel',
            'image'            => 'x-bacon.jpeg',
        ]);

        Product::create([
            'category_id'      => $acompanhamento->id,
            'name'             => 'Batata Frita Especial',
            'price'            => 12.00,
            'description'      => 'Batata palito crocante, temperada com sal e páprica, acompanha molho especial.',
            'preparation_time' => 10,
            'status'           => 'disponivel',
            'image'            => 'batata.jpeg',
        ]);

        Product::create([
            'category_id'      => $bebidas->id,
            'name'             => 'Coca-Cola 2L',
            'price'            => 8.00,
            'description'      => 'Refrigerante Coca-Cola 2L.',
            'preparation_time' => 2,
            'status'           => 'disponivel',
            'image'            => 'coca-cola-2L.jpeg',
        ]);

        Product::create([
            'category_id'      => $combo->id,
            'name'             => 'Combo',
            'price'            => 45.00,
            'description'      => 'X-Burguer + batata frita grande + Coca-Cola 2L.',
            'preparation_time' => 25,
            'status'           => 'disponivel',
            'image'            => 'combo.jpeg',
        ]);
    }
}
