<?php

require_once 'vendor/autoload.php';

use App\Models\Order;

echo "Orders count: " . Order::count() . PHP_EOL;

$last = Order::latest()->first();
if($last) {
    echo "Last order - ID: " . $last->id . ", Client: " . $last->client_name . ", Status: " . $last->status . PHP_EOL;
} else {
    echo "No orders found" . PHP_EOL;
}