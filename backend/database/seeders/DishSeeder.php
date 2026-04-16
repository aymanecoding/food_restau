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
        Dish::create(['name' => 'Briouates au fromage', 'description' => 'Feuilletés croustillants au fromage frais', 'price' => 45.00, 'category_id' => 1, 'image' => 'https://images.unsplash.com/photo-1541599468348-e96984315621?w=400']);
        Dish::create(['name' => 'Salade marocaine', 'description' => 'Tomates, concombres, oignons et citron', 'price' => 40.00, 'category_id' => 1, 'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400']);
        Dish::create(['name' => 'Harira', 'description' => 'Soupe traditionnelle aux légumineuses', 'price' => 35.00, 'category_id' => 1, 'image' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400']);
        Dish::create(['name' => 'Tajine poulet citron', 'description' => 'Poulet fermier aux olives vertes', 'price' => 120.00, 'category_id' => 2, 'image' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=400']);
        Dish::create(['name' => 'Couscous royal', 'description' => 'Agneau, merguez et légumes de saison', 'price' => 150.00, 'category_id' => 2, 'image' => 'https://images.unsplash.com/photo-1541519227354-08fa5d50c44d?w=400']);
        Dish::create(['name' => 'Pastilla au poulet', 'description' => 'Feuilleté sucré-salé à la cannelle', 'price' => 130.00, 'category_id' => 2, 'image' => 'https://images.unsplash.com/photo-1551782450-17144efb5723?w=400']);
        Dish::create(['name' => 'Mechoui d\'agneau', 'description' => 'Épaule d\'agneau rôtie lentement', 'price' => 180.00, 'category_id' => 2, 'image' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=400']);
        Dish::create(['name' => 'Kefta maison', 'description' => 'Brochettes de viande hachée aux herbes', 'price' => 95.00, 'category_id' => 2, 'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400']);
        Dish::create(['name' => 'Chebakia', 'description' => 'Pâtisserie au sésame et miel', 'price' => 30.00, 'category_id' => 3, 'image' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400']);
        Dish::create(['name' => 'Cornes de gazelle', 'description' => 'Petits gâteaux fourrés aux amandes', 'price' => 35.00, 'category_id' => 3, 'image' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400']);
        Dish::create(['name' => 'Thé à la menthe', 'description' => 'Thé vert et menthe fraîche', 'price' => 25.00, 'category_id' => 4, 'image' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=400']);
        Dish::create(['name' => 'Jus d\'orange frais', 'description' => 'Oranges pressées à la commande', 'price' => 30.00, 'category_id' => 4]);
        Dish::create(['name' => 'Eau minérale', 'description' => '50cl, eau minérale naturelle', 'price' => 15.00, 'category_id' => 4, 'image' => 'https://images.unsplash.com/photo-1559839914-17aae19cec71?w=400']);
    }
}
