<?php
// Script pour vérifier les plats dans la base de données
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Dish;
use App\Models\Category;

echo "=== CATÉGORIES ===\n\n";
$categories = Category::all();
echo "Nombre de catégories: " . $categories->count() . "\n";
foreach ($categories as $cat) {
    echo "ID: {$cat->id} - Nom: {$cat->name}\n";
}

echo "\n=== PLATS ===\n\n";
$dishes = Dish::with('category')->get();
echo "Nombre de plats: " . $dishes->count() . "\n\n";

foreach ($dishes as $dish) {
    echo "ID: {$dish->id}\n";
    echo "Nom: {$dish->name}\n";
    echo "Description: " . substr($dish->description, 0, 50) . "...\n";
    echo "Prix: {$dish->price} MAD\n";
    echo "Catégorie: " . ($dish->category ? $dish->category->name : 'Aucune') . " (ID: {$dish->category_id})\n";
    echo "Image: " . (strlen($dish->image) > 50 ? substr($dish->image, 0, 50) . "..." : $dish->image) . "\n";
    echo "-----------------------------------\n";
}

echo "\n=== VÉRIFICATION DES CATÉGORIES PAR DÉFAUT ===\n";
echo "Le frontend utilise ces catégories:\n";
echo "1. Entrées\n";
echo "2. Plats\n";
echo "3. Desserts\n";
echo "4. Boissons\n\n";

echo "Vérifiez que les catégories dans la base correspondent à ces noms.\n";
?>