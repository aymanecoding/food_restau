<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'Entrées', 'description' => 'Plats d\'entrée']);
        Category::create(['name' => 'Plats principaux', 'description' => 'Plats principaux']);
        Category::create(['name' => 'Desserts', 'description' => 'Desserts']);
        Category::create(['name' => 'Boissons', 'description' => 'Boissons']);
    }
}
