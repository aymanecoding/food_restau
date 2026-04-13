<?php
// Test simple de création de commande
$url = 'http://localhost:8000/api/orders';

$data = [
    'items' => [
        ['dish_id' => 1, 'quantity' => 2]
    ],
    'client_name' => 'Test User',
    'client_phone' => '+212612345678',
    'client_address' => 'Test Address, Casablanca',
    'client_note' => 'Test note',
    'payment_method' => 'cash',
    'total_price' => 150
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response:\n";
echo $response ? json_decode($response, true) ? json_encode(json_decode($response), JSON_PRETTY_PRINT) : $response : "No response";
?>
