<?php
// Test script for admin authentication
$apiUrl = 'http://127.0.0.1:8000/api';

// Initialize cURL session
$ch = curl_init();

// Login
$loginData = json_encode([
    'email' => 'admin@dar-el-idrissi.com',
    'password' => 'password'
]);

curl_setopt($ch, CURLOPT_URL, $apiUrl . '/admin/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt'); // Save cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Send cookies

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Login Response Code: $loginHttpCode\n";
echo "Login Response: $loginResponse\n";

// Check authentication
curl_setopt($ch, CURLOPT_URL, $apiUrl . '/admin/check');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$checkResponse = curl_exec($ch);
$checkHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Check Response Code: $checkHttpCode\n";
echo "Check Response: $checkResponse\n";

curl_close($ch);

// Clean up
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}
?>