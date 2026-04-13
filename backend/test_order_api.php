<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\OrderController;

echo "Testing Order API...\n";

// Simulate a request
$request = new Request();
$request->merge([
    'items' => [
        ['dish_id' => 1, 'quantity' => 2]
    ],
    'client_name' => 'Test User',
    'client_phone' => '123456789',
    'client_address' => 'Test Address',
    'client_note' => 'Test Note',
    'payment_method' => 'cash',
    'total_price' => 50.00
]);

$controller = new OrderController();
try {
    $response = $controller->store($request);
    echo "Success: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}