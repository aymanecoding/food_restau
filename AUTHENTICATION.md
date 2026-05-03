# 🔐 Système d'Authentification Dar EL Idrissi

## 📋 Vue d'ensemble

Le système d'authentification utilise **Laravel Sanctum** pour l'API backend et **React Context** pour la gestion de l'état frontend. Les tokens sont stockés dans `localStorage` et ajoutés automatiquement à toutes les requêtes API.

---

## 🚀 Points d'accès (Endpoints)

### Routes Publiques (Sans authentification)
```
POST   /api/register                  (Créer un compte)
POST   /api/login                     (Se connecter)
GET    /api/categories                (Lire les catégories)
GET    /api/dishes                    (Lire tous les plats)
GET    /api/dishes/:id                (Lire un plat)
POST   /api/test-order                (Test API)
```

### Routes Protégées (Authentification requise)
```
GET    /api/me                        (Informations utilisateur)
POST   /api/logout                    (Se déconnecter)
POST   /api/revoke-all-tokens         (Révoquer tous les tokens)

GET    /api/orders                    (Lister les commandes)
POST   /api/orders                    (Créer une commande)
GET    /api/orders/:id                (Détails d'une commande)
PUT    /api/orders/:id                (Mettre à jour une commande)
DELETE /api/orders/:id                (Supprimer une commande)

POST   /api/dishes                    (Créer un plat - Admin)
PUT    /api/dishes/:id                (Mettre à jour un plat - Admin)
DELETE /api/dishes/:id                (Supprimer un plat - Admin)
```

---

## 🔧 Architecture

### Backend (`/backend`)

#### 1. AuthController (`app/Http/Controllers/AuthController.php`)
Gère l'authentification avec 5 méthodes :

```php
register()           // Inscription avec email/password
login()              // Connexion et génération de token
me()                 // Retourner l'utilisateur connecté
logout()             // Déconnecter (révoquer le token)
revokeAllTokens()    // Révoquer tous les tokens de l'utilisateur
```

**Exemple de réponse login :**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@example.com"
  },
  "token": "1|ABC123XYZ..."
}
```

#### 2. Routes API (`routes/api.php`)
- Routes publiques sans middleware
- Routes protégées avec `middleware('auth:sanctum')`
- CORS déjà configuré par Laravel

### Frontend (`/frontend/src`)

#### 1. API Client (`api/apiClient.js`)
Centralise tous les appels API avec gestion automatique des tokens :

```javascript
// Inclut le token Bearer automatiquement
apiCall(endpoint, options)

// Spécialisé par ressource :
authAPI.register()    authAPI.login()
authAPI.logout()      authAPI.me()

categoriesAPI.getAll()

dishesAPI.getAll()    dishesAPI.getById()
dishesAPI.create()    dishesAPI.update()
dishesAPI.delete()

ordersAPI.getAll()    ordersAPI.getById()
ordersAPI.create()    ordersAPI.update()
ordersAPI.delete()
```

#### 2. Hook useAuth (`hooks/useAuth.js`)
Gère l'état d'authentification avec **React Context** :

```javascript
const { 
  user,              // { id, name, email }
  token,             // Le token Sanctum
  loading,           // État de chargement
  error,             // Messages d'erreur
  isAuthenticated,   // Boolean true/false
  login,             // Fonction login(email, password)
  register,          // Fonction register(name, email, password, confirmation)
  logout             // Fonction logout()
} = useAuth();
```

#### 3. Composants d'Authentification
- `LoginForm.jsx` - Formulaire de connexion
- `RegisterForm.jsx` - Formulaire d'inscription
- `ProtectedRoute.jsx` - Composant wrapper pour pages protégées

#### 4. Middleware d'authentification
- Stockage du token dans `localStorage` (clés : `auth_token`, `auth_user`)
- Rechargement automatique du token au démarrage
- Redirection vers `/login` si session expirée (HTTP 401)

---

## 💻 Utilisation

### Backend - Démarrer le serveur

```bash
cd backend

# Si première utilisation, créer un utilisateur test
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password123')])

# Démarrer Laravel
php artisan serve
```

### Frontend - Utiliser l'authentification

```javascript
// 1. Accès au contexte d'authentification
import { useAuth } from "./hooks/useAuth";

function MyComponent() {
  const { isAuthenticated, user, login, logout } = useAuth();

  if (!isAuthenticated) {
    return <LoginForm />;
  }

  return (
    <div>
      <p>Bienvenue {user.name}!</p>
      <button onClick={logout}>Déconnecter</button>
    </div>
  );
}

// 2. Utiliser les APIs protégées
import { ordersAPI, dishesAPI } from "./api/apiClient";

async function loadOrders() {
  try {
    const orders = await ordersAPI.getAll(); // Token ajouté automatiquement
    console.log(orders);
  } catch (err) {
    console.error("Erreur:", err.message);
  }
}

// 3. Créer un plat (admin)
async function createDish() {
  const dish = await dishesAPI.create({
    name: "Tajine Agneau",
    description: "Savoureux tajine",
    price: 120,
    category_id: 1,
    image: "data:image/jpeg;base64,..."
  });
}
```

---

## 🔑 Flux d'Authentification

```
┌─────────────┐
│  Frontend   │
│  Utilisateur│
└──────┬──────┘
       │ Fait: login(email, password)
       ▼
┌─────────────────────────────┐
│  POST /api/login            │
├─────────────────────────────┤
└──────┬──────────────────────┘
       │ Retour: { token, user }
       ▼
┌─────────────────────────────┐
│ localStorage:               │
│ - auth_token = "1|ABC..."   │
│ - auth_user = {...}         │
└─────────────────────────────┘
       │ Pour les requêtes suivantes:
       │ Header: Authorization: Bearer 1|ABC...
       ▼
┌─────────────────────────────┐
│  API Protégée               │
│  ☑ décodage du token        │
│  ☑ vérification utilisateur │
│  ✓ requête acceptée        │
└─────────────────────────────┘
```

---

## ⚙️ Configuration

### Variables d'environnement Frontend

Créer `.env` à la racine du frontend :

```bash
REACT_APP_API_URL=http://localhost:8000/api
```

### CORS Backend

Déjà configuré dans `config/cors.php` (Laravel 10+).
À ajuster si le frontend est sur un autre domaine :

```php
'allowed_origins' => [
    'http://localhost:3000',          // Dev Frontend
    'http://localhost:8000',          // Dev Backend
    'https://app.example.com',        // Production
],
```

---

## 🧪 Tests

### Test d'inscription via cURL

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Test de connexion

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@test.com",
    "password": "password123"
  }'
```

### Test d'une route protégée

```bash
curl -X GET http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 🔒 Sécurité

- ✅ Tokens stockés en localStorage (accessible en JavaScript)
- ✅ Tokens incluent automatiquement le `Bearer` pour HTTP
- ✅ Expiration automatique en cas d'erreur 401
- ✅ Refresh automatique au rechargement de page
- ✅ Passwords hashés avec bcrypt
- ✅ Sanctum valide chaque requête

**⚠️ Important :** Pour la production, considérer :
- Stocker les tokens en HttpOnly cookies (plus sécurisé)
- Implémenter refresh token strategy
- Ajouter rate limiting sur login/register
- Activer HTTPS

---

## 📚 Fichiers clés

```
Backend:
├── app/Http/Controllers/AuthController.php     [NEW]
└── routes/api.php                              [MODIFIÉ]

Frontend:
├── api/apiClient.js                            [NEW]
├── hooks/useAuth.js                            [NEW]
├── components/LoginForm.jsx                    [NEW]
├── components/RegisterForm.jsx                 [NEW]
├── components/ProtectedRoute.jsx               [NEW]
├── styles/AuthForms.css                        [NEW]
├── hooks/useOrders.js                          [MODIFIÉ]
└── index.js                                    [MODIFIÉ]
```

---

## 🚀 Prochaines étapes

1. **Tester le flux complet** : register → login → accès APIs protégées
2. **Adapter AdminDashboard** pour utiliser useAuth au lieu de adminLoggedIn
3. **Ajouter refresh token** pour plus de sécurité
4. **Tester en production** avec HTTPS
5. **Ajouter two-factor authentication** (optionnel)

---

**Créé:** Avril 2026 | Dar EL Idrissi Food App
