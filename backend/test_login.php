<?php
// Test complet de connexion admin
$apiUrl = 'http://127.0.0.1:8000/api';

echo "=== TEST DE CONNEXION ADMIN ===\n\n";

// Login avec les bons identifiants
$ch = curl_init();

$loginData = json_encode([
    'email' => 'admin@dar-el-idrissi.com',
    'password' => '1234'
]);

echo "Tentative de connexion avec:\n";
echo "  Email: admin@dar-el-idrissi.com\n";
echo "  Mot de passe: 1234\n\n";

curl_setopt($ch, CURLOPT_URL, $apiUrl . '/admin/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Code HTTP: $loginHttpCode\n";
echo "Réponse: $loginResponse\n\n";

$loginResult = json_decode($loginResponse, true);

if ($loginHttpCode == 200 && isset($loginResult['token'])) {
    $token = $loginResult['token'];
    echo "✅ Connexion réussie!\n";
    echo "Token: " . substr($token, 0, 20) . "...\n\n";
    
    // Test du endpoint check avec le token
    echo "=== TEST DU ENDPOINT CHECK ===\n";
    curl_setopt($ch, CURLOPT_URL, $apiUrl . '/admin/check');
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    
    $checkResponse = curl_exec($ch);
    $checkHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "Code HTTP: $checkHttpCode\n";
    echo "Réponse: $checkResponse\n\n";
    
    if ($checkHttpCode == 200) {
        echo "✅ Check authentification réussi!\n";
    } else {
        echo "❌ Échec du check\n";
    }
} else {
    echo "❌ Échec de la connexion\n";
    if ($loginHttpCode == 401) {
        echo "Les identifiants sont incorrects.\n";
        echo "Vérifiez que le mot de passe est correct.\n";
    }
}

curl_close($ch);
?>