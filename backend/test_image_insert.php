<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Dish;
use App\Models\Category;

echo "Testing large image insert...\n";

try {
    $testImage = str_repeat('a', 10000); // 10KB test image
    
    $dish = Dish::create([
        'name' => 'test_large_image',
        'description' => 'test',
        'price' => 10.00,
        'image' => $testImage,
        'category_id' => 1
    ]);
    
    echo "SUCCESS: Created dish with ID " . $dish->id . "\n";
    echo "Image length: " . strlen($dish->image) . " bytes\n";
    
    // Clean up test
    $dish->delete();
    echo "Test dish deleted.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}