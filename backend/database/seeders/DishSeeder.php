<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dish;

class DishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dish::create(['name' => 'Salade César', 'description' => 'Salade fraîche', 'price' => 8.50, 'category_id' => 1]);
        Dish::create(['name' => 'Burger', 'description' => 'Burger au boeuf', 'price' => 12.00, 'category_id' => 2]);
        Dish::create(['name' => 'Tiramisu', 'description' => 'Dessert italien', 'price' => 6.00, 'category_id' => 3]);
        Dish::create(['name' => 'Coca-Cola', 'description' => 'Boisson gazeuse', 'price' => 2.50, 'category_id' => 4]);
    }
}
