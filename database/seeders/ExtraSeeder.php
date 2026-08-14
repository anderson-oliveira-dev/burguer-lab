<?php

namespace Database\Seeders;

use App\Models\Extra;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $extras = [
            ['name' => 'Salada', 'price' => 2.0],
            ['name' => 'Bacon', 'price' => 6.0],
            ['name' => 'Cebola Caramelizada', 'price' => 4.0],
            ['name' => 'Calabresa', 'price' => 4.0],
            ['name' => 'Ovo', 'price' => 3.0],
            ['name' => 'Cheddar Cremoso', 'price' => 4.0],
            ['name' => 'Cream Cheese', 'price' => 4.0],
            ['name' => 'Queijo Muçarela', 'price' => 5.0],
        ];

        foreach ($extras as $extra) {
            Extra::firstOrCreate(
                ['name' => $extra['name']],
                ['price' => $extra['price']]
            );
        }
    }
}
