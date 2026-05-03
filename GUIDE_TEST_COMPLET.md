# 🧪 GUIDE DE TEST COMPLET - PROJET RESTAURATION

## ✅ ÉTAT ACTUEL DES TESTS

### Backend (Laravel)
- ✅ Serveur démarré sur `http://127.0.0.1:8000`
- ✅ Migrations exécutées avec succès
- ✅ Base de données configurée (`food_app_db`)
- ✅ Routes API publiques fonctionnent (`/api/categories`, `/api/dishes`)
- ✅ Authentification admin testée avec succès
- ✅ Admin par défaut présent : `admin@dar-el-idrissi.com` / `1234`

### Frontend (React)
- ✅ Application démarrée sur `http://localhost:3000`
- ✅ Compilation réussie sans erreurs

---

## 📋 PROCÉDURE DE TEST ÉTAPE PAR ÉTAPE

### 1. TEST DE L'AUTHENTIFICATION ADMIN

#### Étape 1.1 : Accéder au site
1. Ouvrez votre navigateur
2. Allez à l'adresse : `http://localhost:3000`
3. Vous devriez voir la page d'accueil du restaurant

#### Étape 1.2 : Se connecter en tant qu'admin
1. Cliquez sur le bouton "Admin" ou "Connexion" dans la navbar
2. Ou accédez directement à : `http://localhost:3000/admin-login`
3. Entrez les identifiants :
   - **Email** : `admin@dar-el-idrissi.com`
   - **Mot de passe** : `1234`
4. Cliquez sur "Se connecter"

**Résultat attendu** :
- ✅ Redirection vers le dashboard admin (`http://localhost:3000/admin`)
- ✅ Affichage de l'interface d'administration avec 3 onglets :
  - Commandes
  - Menu
  - Paramètres
- ✅ Nom de l'admin affiché dans la sidebar

#### Étape 1.3 : Tester les fonctionnalités admin

**Onglet Commandes** :
- Vérifiez que la liste des commandes s'affiche
- Testez le changement de statut d'une commande
- ✅ Les statuts devraient être : Nouvelle, Confirmée, En préparation, En livraison, Livrée, Annulée

**Onglet Menu** :
- Vérifiez que la liste des plats s'affiche
- Testez l'ajout d'un nouveau plat
- Testez la modification d'un plat existant
- Testez la suppression d'un plat
- ✅ Toutes les opérations devraient fonctionner

**Onglet Paramètres** :
- Vérifiez les paramètres du restaurant

#### Étape 1.4 : Déconnexion
1. Cliquez sur "Déconnexion" dans la sidebar
2. ✅ Retour à la page d'accueil

---

### 2. TEST DE L'AUTHENTIFICATION CLIENT

#### Étape 2.1 : Créer un compte client
1. Allez à : `http://localhost:3000/register` (si la page existe)
2. Ou cliquez sur "Inscription" dans la navbar
3. Remplissez le formulaire :
   - Nom : `Test Client`
   - Email : `client@test.com`
   - Mot de passe : `password123`
   - Confirmation mot de passe : `password123`
4. Cliquez sur "S'inscrire"

**Résultat attendu** :
- ✅ Compte créé avec succès
- ✅ Token stocké dans localStorage
- ✅ Redirection vers la page d'accueil ou dashboard

#### Étape 2.2 : Se connecter en tant que client
1. Allez à : `http://localhost:3000/login`
2. Entrez les identifiants :
   - **Email** : `client@test.com`
   - **Mot de passe** : `password123`
3. Cliquez sur "Se connecter"

**Résultat attendu** :
- ✅ Connexion réussie
- ✅ Nom de l'utilisateur affiché dans la navbar
- ✅ Panier fonctionnel

---

### 3. TEST DU PARCOURS CLIENT (COMMANDE)

#### Étape 3.1 : Consulter le menu
1. Allez à : `http://localhost:3000/menu`
2. ✅ Les plats devraient s'afficher par catégorie
3. ✅ Les images, prix et descriptions devraient être visibles

#### Étape 3.2 : Ajouter au panier
1. Cliquez sur "Ajouter au panier" pour un plat
2. ✅ Notification toast affichée
3. ✅ Le compteur du panier dans la navbar augmente
4. ✅ Le panier s'ouvre (drawer)

#### Étape 3.3 : Passer une commande
1. Ouvrez le panier (clic sur l'icône panier)
2. Ajustez les quantités si nécessaire
3. Cliquez sur "Commander"
4. Remplissez le formulaire de commande :
   - Nom : `Jean Test`
   - Téléphone : `0612345678`
   - Adresse : `123 Rue Test, Marrakech`
   - Note : (optionnel)
   - Mode de paiement : `À la livraison`
5. Cliquez sur "Confirmer la commande"

**Résultat attendu** :
- ✅ Commande créée avec succès
- ✅ Numéro de commande affiché
- ✅ Redirection vers la page de confirmation
- ✅ Panier vidé

---

### 4. TEST DE L'INTERFACE ADMIN (GESTION DES COMMANDES)

#### Étape 4.1 : Voir les nouvelles commandes
1. Connectez-vous en admin
2. Allez dans l'onglet "Commandes"
3. ✅ La commande créée devrait apparaître en premier
4. ✅ Les détails devraient être corrects :
   - Client, téléphone, adresse
   - Items commandés
   - Total
   - Statut "Nouvelle"

#### Étape 4.2 : Modifier le statut
1. Cliquez sur le statut "Nouvelle"
2. Sélectionnez "Confirmée"
3. ✅ Le statut devrait se mettre à jour
4. ✅ La commande devrait apparaître avec le nouveau statut

---

## 🔍 PROBLÈMES COURANTS ET SOLUTIONS

### Problème 1 : Erreur de connexion API
**Symptôme** : Les plats ne se chargent pas, erreur de connexion
**Solution** :
1. Vérifiez que le backend tourne : `http://127.0.0.1:8000/api/categories`
2. Vérifiez que le frontend est configuré pour utiliser la bonne URL API
3. Redémarrez les deux serveurs

### Problème 2 : Erreur CORS
**Symptôme** : Messages d'erreur CORS dans la console
**Solution** :
1. Vérifiez que le middleware CORS est bien configuré dans `backend/app/Http/Kernel.php`
2. Vérifiez que `config/cors.php` autorise `localhost:3000`
3. Redémarrez le serveur Laravel

### Problème 3 : Token expiré ou invalide
**Symptôme** : Déconnexion automatique, erreur 401
**Solution** :
1. Déconnectez-vous et reconnectez-vous
2. Vérifiez que le token est bien stocké dans localStorage
3. Ouvrez les DevTools (F12) > Application > Local Storage pour vérifier

### Problème 4 : Admin non reconnu
**Symptôme** : Impossible d'accéder au dashboard admin
**Solution** :
1. Vérifiez que l'utilisateur a `is_admin = true` dans la base de données
2. Exécutez : `php backend/check_users.php` pour vérifier
3. Si nécessaire, recréez un admin avec :
   ```bash
   cd backend
   php artisan tinker
   >>> User::create(['name'=>'Admin','email'=>'admin@test.com','password'=>bcrypt('password123'),'is_admin'=>true])
   ```

### Problème 5 : Images non affichées
**Symptôme** : Les images des plats ne s'affichent pas
**Solution** :
1. Vérifiez que les URLs d'images sont valides
2. Vérifiez que le champ `image` dans la table `dishes` contient des URLs complètes
3. Testez une image dans un navigateur pour vérifier

---

## 🛠️ COMMANDES UTILES POUR LE DÉVELOPPEMENT

### Backend
```bash
# Démarrer le serveur
cd backend
php artisan serve --host=127.0.0.1 --port=8000

# Vérifier les utilisateurs
php check_users.php

# Tester la connexion admin
php test_login.php

# Exécuter les seeders
php artisan db:seed

# Créer un admin manuellement
php artisan tinker
>>> User::create(['name'=>'Admin','email'=>'admin@test.com','password'=>bcrypt('password123'),'is_admin'=>true])
```

### Frontend
```bash
# Démarrer le serveur de développement
cd frontend
npm start

# Construire pour la production
npm run build

# Exécuter les tests
npm test
```

---

## 📊 VÉRIFICATION FINALE

### Backend
- [x] Serveur Laravel démarré
- [x] Base de données connectée
- [x] Migrations exécutées
- [x] Admin par défaut présent
- [x] Routes API fonctionnelles
- [x] Authentification admin testée

### Frontend
- [x] Serveur React démarré
- [x] Compilation réussie
- [ ] Navigation vers pages fonctionne
- [ ] Authentification admin fonctionne
- [ ] Authentification client fonctionne
- [ ] CRUD plats fonctionne
- [ ] Système de commandes fonctionne
- [ ] Panier fonctionne

---

## 🎯 PROCHAINES ÉTAPES

Une fois les tests de base validés, vous pouvez :

1. **Ajouter des fonctionnalités** :
   - Upload d'images pour les plats
   - Pagination des commandes
   - Recherche et filtres
   - Export PDF/Excel des commandes

2. **Améliorer l'UX** :
   - Confirmations avant suppression
   - Messages d'erreur plus détaillés
   - Loading states
   - Responsive design

3. **Sécurité** :
   - Rate limiting sur les endpoints d'auth
   - Validation renforcée
   - HTTPS en production

4. **Performance** :
   - Caching des données
   - Optimisation des requêtes
   - Compression des images

---

## 📞 BESOIN D'AIDE ?

Si vous rencontrez des problèmes pendant les tests :

1. **Vérifiez les logs** :
   - Backend : `backend/storage/logs/laravel.log`
   - Frontend : Console du navigateur (F12)

2. **Testez les endpoints API** avec les scripts PHP fournis :
   - `backend/test_login.php` - Test connexion admin
   - `backend/check_users.php` - Vérifier les utilisateurs

3. **Redémarrez les serveurs** :
   - Backend : Ctrl+C puis `php artisan serve`
   - Frontend : Ctrl+C puis `npm start`

Bon courage pour les tests ! 🚀