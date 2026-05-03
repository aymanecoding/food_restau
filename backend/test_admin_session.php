<?php

$payload = json_encode([
    'email' => 'admin@dar-el-idrissi.com',
    'password' => 'password123',
]);

$opts = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $payload,
        'ignore_errors' => true,
    ],
];

$ctx = stream_context_create($opts);
$response = file_get_contents('http://localhost:8000/api/admin/login', false, $ctx);

echo "LOGIN RESPONSE HEADERS:\n";
foreach ($http_response_header as $header) {
    echo $header . "\n";
}

echo "\nLOGIN BODY:\n";
echo $response . "\n";
