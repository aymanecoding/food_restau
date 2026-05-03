<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\DishController;

echo "Testing Dish API...\n";

// Simulate a request
$request = new Request();

$controller = new DishController();
try {
    $response = $controller->index($request);
    $content = $response->getContent();
    echo "Response content length: " . strlen($content) . "\n";
    echo "First 500 characters:\n";
    echo substr($content, 0, 500) . "\n";
    echo "Is JSON valid: " . (json_decode($content) !== null ? 'Yes' : 'No') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}