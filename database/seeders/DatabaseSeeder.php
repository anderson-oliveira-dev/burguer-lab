<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(CategorySeeder::class);

        $this->call(ExtraSeeder::class);

        $categories = Category::all();

        Product::factory(20)->make()->each(function ($product) use ($categories) {
            $product->category_id = $categories->random()->id;
            $product->save();
        });

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'type' => 'admin',
            'password' => bcrypt('admin')
        ]);
    }
}
