# 🔧 Guide d'implémentation - Amélioration sécurité authentification admin

Ce guide fournit les étapes pour implémenter les améliorations de sécurité pour l'authentification admin.

---

## Plan d'action

### **Phase 1: Distinction Admin/Client (CRITIQUE)**

#### Étape 1.1 - Créer une migration pour ajouter `role`

```bash
php artisan make:migration add_role_to_users_table --table=users
```

**Contenu de la migration:**
```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'client'])->default('client')->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
```

**Exécuter:**
```bash
php artisan migrate
```

#### Étape 1.2 - Mettre à jour le modèle User

**Ajouter au modèle [backend/app/Models/User.php](backend/app/Models/User.php):**

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',  // Ajouter cette ligne
];

// Ajouter cette méthode
public function isAdmin(): bool
{
    return $this->role === 'admin';
}
```

#### Étape 1.3 - Vérifier le rôle dans le contrôleur

**Modifier [backend/app/Http/Controllers/AdminAuthController.php](backend/app/Http/Controllers/AdminAuthController.php):**

```php
// Ligne 30-31, après la vérification du mot de passe
if (!$user || !Hash::check($request->password, $user->password)) {
    throw ValidationException::withMessages([
        'username' => ['Identifiants incorrects.'],
    ]);
}

// ✅ AJOUTER: Vérifier que c'est un admin
if (!$user->isAdmin()) {
    throw ValidationException::withMessages([
        'username' => ['Accès administrateur refusé.'],
    ]);
}

// Créer le token...
```

---

### **Phase 2: Sécurité de base**

#### Étape 2.1 - Ajouter Rate Limiting

**Créer un Middleware [backend/app/Http/Middleware/ThrottleAdminLogin.php](backend/app/Http/Middleware/ThrottleAdminLogin.php):**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ThrottleAdminLogin
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'admin-login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5, 15)) {
            // Max 5 tentatives par 15 minutes
            return response()->json([
                'success' => false,
                'message' => 'Trop de tentatives. Réessayez dans ' . 
                    RateLimiter::availableIn($key) . ' secondes.',
            ], 429);
        }

        RateLimiter::hit($key);

        return $next($request);
    }
}
```

**Enregistrer dans [backend/app/Http/Kernel.php](backend/app/Http/Kernel.php):**

```php
protected $middlewareAliases = [
    // ... autres middlewares
    'throttle.admin' => \App\Http\Middleware\ThrottleAdminLogin::class,
];
```

**Utiliser dans [backend/routes/api.php](backend/routes/api.php):**

```php
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle.admin');
    // ... autres routes
});
```

#### Étape 2.2 - Ajouter Logging

**Modifier [backend/app/Http/Controllers/AdminAuthController.php](backend/app/Http/Controllers/AdminAuthController.php):**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;  // ✅ AJOUTER
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->username)
                    ->orWhere('name', $request->username)
                    ->first();

        // ✅ AJOUTER: Log de tentative échouée
        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::warning('Admin login attempt failed', [
                'username' => $request->username,
                'ip' => $request->ip(),
                'user_exists' => (bool) $user,
                'timestamp' => now(),
            ]);
            
            throw ValidationException::withMessages([
                'username' => ['Identifiants incorrects.'],
            ]);
        }

        // ✅ AJOUTER: Vérifier le rôle admin
        if (!$user->isAdmin()) {
            Log::warning('Non-admin login attempt', [
                'username' => $request->username,
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);
            
            throw ValidationException::withMessages([
                'username' => ['Accès administrateur refusé.'],
            ]);
        }

        // ✅ AJOUTER: Log de succès
        Log::info('Admin login successful', [
            'admin_id' => $user->id,
            'admin_email' => $user->email,
            'ip' => $request->ip(),
            'timestamp' => now(),
        ]);

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion administrateur réussie',
            'admin' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }
    
    // ... reste du code
}
```

---

### **Phase 3: Nettoyer le frontend**

#### Étape 3.1 - Supprimer les identifiants affichés

**Modifier [frontend/src/admin/AdminLogin.jsx](frontend/src/admin/AdminLogin.jsx):**

```jsx
// ❌ SUPPRIMER cette section:
            <div className="text-center text-xs text-stone-400 space-y-1">
              <p>Admins disponibles:</p>
              <p>admin@dar-el-idrissi.com</p>
              <p>manager@dar-el-idrissi.com</p>
              <p className="text-stone-300">Mot de passe: password</p>
            </div>

// ✅ REMPLACER par:
            <div className="text-center text-xs text-stone-500 space-y-1">
              <p>Veuillez entrer vos identifiants d'administrateur</p>
            </div>
```

---

### **Phase 4: Seed des données (optionnel)**

**Créer un seeder [backend/database/seeders/AdminUserSeeder.php](backend/database/seeders/AdminUserSeeder.php):**

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@dar-el-idrissi.com',
            'password' => Hash::make('password'), // ⚠️ Changer en production!
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Manager',
            'email' => 'manager@dar-el-idrissi.com',
            'password' => Hash::make('password'), // ⚠️ Changer en production!
            'role' => 'admin',
        ]);
    }
}
```

**Exécuter:**
```bash
php artisan db:seed --class=AdminUserSeeder
```

---

## 🧪 Vérification après implémentation

### Test 1: Vérifier le rôle admin
```bash
# Accéder à la base de données
php artisan tinker

# Vérifier qu'un utilisateur admin existe
User::where('email', 'admin@dar-el-idrissi.com')->first()

# Vérifier le rôle
User::where('email', 'admin@dar-el-idrissi.com')->first()->isAdmin()  # true
```

### Test 2: Tester la connexion
```bash
curl -X POST http://localhost:8000/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin@dar-el-idrissi.com","password":"password"}'

# Réponse attendue:
{
  "success": true,
  "message": "Connexion administrateur réussie",
  "token": "...",
  "admin": {...}
}
```

### Test 3: Vérifier les logs
```bash
tail -f storage/logs/laravel.log
```

---

## 📋 Checklist de mise en œuvre

- [ ] Créer la migration `add_role_to_users_table`
- [ ] Exécuter la migration
- [ ] Mettre à jour le modèle User
- [ ] Ajouter la vérification de rôle dans AdminAuthController
- [ ] Créer le middleware ThrottleAdminLogin
- [ ] Enregistrer le middleware dans Kernel.php
- [ ] Appliquer le middleware sur la route login
- [ ] Ajouter les logs dans AdminAuthController
- [ ] Supprimer les identifiants affichés du frontend
- [ ] Tester la connexion
- [ ] Vérifier les logs
- [ ] Créer le seeder AdminUserSeeder
- [ ] Réinitialiser les données avec le seeder

---

## 🚨 Considérations de sécurité pour la production

1. **HTTPS obligatoire** - Assurez-vous que le site fonctionne en HTTPS
2. **Mots de passe forts** - Changez les mots de passe par défaut
3. **Variables d'environnement** - Utilisez `.env` pour les configurations sensibles
4. **CORS configuré** - Vérifiez les origins autorisés
5. **CSRF Protection** - Activée par défaut dans Laravel
6. **Sanctum** - Vérifiez la configuration dans `config/sanctum.php`

---

## 📚 Ressources utiles

- [Laravel Authentication Documentation](https://laravel.com/docs/10.x/authentication)
- [Laravel Sanctum Documentation](https://laravel.com/docs/10.x/sanctum)
- [Laravel Hash Documentation](https://laravel.com/docs/10.x/hashing)
- [OWASP Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html)
