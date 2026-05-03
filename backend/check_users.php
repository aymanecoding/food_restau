<?php
// Script pour vérifier les utilisateurs dans la base de données
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== UTILISATEURS DANS LA BASE DE DONNÉES ===\n\n";

$users = User::all();
echo "Nombre d'utilisateurs: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Nom: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Admin: " . ($user->is_admin ? 'Oui' : 'Non') . "\n";
    echo "Créé le: {$user->created_at}\n";
    echo "-----------------------------------\n";
}

echo "\n=== TEST DE CONNEXION ===\n";
echo "Essayez de vous connecter avec:\n";
echo "Email: admin@dar-el-idrissi.com\n";
echo "Mot de passe: 1234\n";
?>