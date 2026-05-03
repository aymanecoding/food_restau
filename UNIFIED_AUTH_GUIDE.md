# 🔐 Test d'Authentification Unifiée

## Modifications Effectuées

### Backend
- ✅ Migration pour ajouter `is_admin` à la table users
- ✅ Modification de AuthController pour retourner `is_admin` dans les réponses
- ✅ Modification de AuthController.me() pour retourner `is_admin`
- ✅ AdminSeeder créé avec utilisateurs de test:
  - **Admin**: admin@example.com / password
  - **Client**: client@example.com / password

### Frontend
- ✅ Mise à jour du hook `useAuth` avec propriété `isAdmin`
- ✅ Création de `LoginPage.jsx` unifiée (connexion + inscription)
- ✅ Modification de `Navbar.jsx` avec:
  - ✅ Bouton "🔑 Connexion" près du panier
  - ✅ Menu utilisateur avec déconnexion
  - ✅ Affichage "🔐 Administrateur" ou "👤 Client"
  - ✅ Accès à l'espace Admin pour les admins
- ✅ Modification de `App.js` pour:
  - ✅ Redirection automatique vers Admin si is_admin = true
  - ✅ Redirection vers Home si client se connecte
  - ✅ Protection de la page Admin (vérifie le rôle)
- ✅ Ajout des icônes lock et user

## Flux d'Authentification

### Utilisateur Admin
1. Clique sur "🔑 Connexion" (dans la Navbar)
2. Se connecte avec admin@example.com / password
3. **Redirection automatique vers Espace Admin** ✅
4. Accès à la gestion des plats, commandes, etc.

### Utilisateur Client
1. Clique sur "🔑 Connexion"
2. Se connecte ou s'inscrit
3. **Reste dans l'espace client** ✅
4. Peut voir "📦 Mes commandes"
5. Peut accéder au panier et commander

### Non Authentifié
1. Voir "🔑 Connexion" près du panier
2. Bouton disparaît une fois connecté
3. Un menu utilisateur apparaît

## Tests à Effectuer

### Test 1: Connexion Admin
```
Email: admin@example.com
Password: password
→ Doit rediriger vers "Espace Admin"
```

### Test 2: Connexion Client
```
Email: client@example.com
Password: password
→ Doit rediriger vers "Accueil"
→ Doit afficher "👤 Client"
```

### Test 3: Inscription
```
Nom: Jean Dupont
Email: jean@example.com
Password: password123
Confirmez: password123
→ Doit créer un compte client
→ Doit rediriger vers "Accueil"
```

### Test 4: Menu Utilisateur
```
- Doit afficher le nom et email
- Doit afficher le rôle (Admin ou Client)
- Admin voit "⚙️ Espace Admin"
- Tous voient "📦 Mes commandes"
- Tous voient "🚪 Déconnexion"
```

### Test 5: Protection Admin
```
- Client ne peut pas accéder à /admin
- Si on force l'URL admin en tant que client
→ Redirection vers Accueil avec message d'erreur
```

## Architecture

### Unified Auth Flow
```
LoginPage.jsx
  ├── login(email, password)
  │   └── AuthController.login()
  │       └── Retourne: user { id, name, email, is_admin }
  │
  ├── register(name, email, password)
  │   └── AuthController.register()
  │       └── Retourne: user { id, name, email, is_admin: false }
  │
  └── Redirection auto basée sur is_admin
      ├── Si is_admin = true → Admin Dashboard
      └── Si is_admin = false → Home + message de bienvenue
```

### Navigation
```
Navbar
  ├── Si non connecté → "🔑 Connexion"
  └── Si connecté → Menu utilisateur
      ├── Affiche: Nom, Email, Rôle
      ├── Si Admin → "⚙️ Espace Admin"
      ├── Tous → "📦 Mes commandes"
      └── Tous → "🚪 Déconnexion"
```

## Bases de Données

### Users Table (Avant/Après)
```
Avant:
- id, name, email, password, remember_token, created_at, updated_at

Après:
- id, name, email, is_admin (NEW), password, remember_token, created_at, updated_at
```

## Sécurité

✅ Tokens Sanctum utilisés pour les sessions
✅ Vérification is_admin côté backend
✅ Protection des routes Admin avec middleware
✅ Redirection automatique si accès non autorisé
✅ Déconnexion révoque le token

## Points Clés

1. **Une seule authentification** pour Admin et Client
2. **Rôle déterminé par `is_admin`** lors de la connexion
3. **Pas d'espace admin séparé** - contrôle par rôle
4. **Menu utilisateur** au lieu d'un bouton "Espace Admin"
5. **Inscription crée automatiquement** des clients (is_admin = false)
6. **Admins créés** via seeder ou base de données
