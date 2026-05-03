# 🐛 DEBUG - RIEN NE S'AFFICHE DANS L'ADMIN

## 🔍 PROBLÈME
Rien ne s'affiche dans le dashboard admin, ni les commandes ni les plats.

## ✅ ACTIONS ENTREPRISES

### 1. Ajout de logs de debug dans AdminDashboard.jsx
J'ai modifié le fichier `frontend/src/admin/AdminDashboard.jsx` pour ajouter des logs détaillés :

```javascript
const loadOrders = async () => {
  try {
    console.log("🔄 Chargement des commandes...");
    const data = await adminOrdersAPI.getAll();
    console.log("✅ Commandes chargées:", data);
    setOrders(data || []);
  } catch (err) {
    console.error("❌ Erreur chargement commandes:", err);
    notify("Erreur chargement commandes: " + (err.message || "Erreur inconnue"), "error");
    setOrders([]);
  }
};

const loadMenuItems = async () => {
  try {
    console.log("🔄 Chargement du menu...");
    const data = await adminDishesAPI.getAll();
    console.log("✅ Menu chargé:", data);
    setMenuItems(data || []);
  } catch (err) {
    console.error("❌ Erreur chargement menu:", err);
    notify("Erreur chargement menu: " + (err.message || "Erreur inconnue"), "error");
    setMenuItems([]);
  } finally {
    setLoading(false);
  }
};
```

## 📋 PROCÉDURE DE DEBUG

### Étape 1 : Ouvrez la console du navigateur
1. Appuyez sur **F12** pour ouvrir les DevTools
2. Allez dans l'onglet **Console**

### Étape 2 : Connectez-vous en admin
1. Allez sur `http://localhost:3000/admin-login`
2. Connectez-vous avec `admin@dar-el-idrissi.com` / `1234`

### Étape 3 : Observez les logs
Vous devriez voir apparaître :
- `🔄 Chargement des commandes...`
- `✅ Commandes chargées: ...` (avec les données)
- `🔄 Chargement du menu...`
- `✅ Menu chargé: ...` (avec les données)

### Étape 4 : Partagez les logs
Copiez-collez TOUT le contenu de la console ici, y compris :
- Les logs avec ✅
- Les erreurs avec ❌
- Les messages de réseau

## 🔧 TESTS SUPPLÉMENTAIRES

### Test 1 : Vérifier le token admin
Dans la console, exécutez :
```javascript
console.log("Token admin:", localStorage.getItem('admin_token'));
```

### Test 2 : Tester l'API manuellement
Dans la console, exécutez :
```javascript
const token = localStorage.getItem('admin_token');
fetch('http://localhost:8000/api/admin/dishes', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(r => r.json())
.then(d => console.log("Plats API:", d))
.catch(e => console.error("Erreur API:", e));
```

### Test 3 : Vérifier le réseau
1. Dans DevTools, allez dans l'onglet **Network**
2. Filtrez par **XHR**
3. Rechargez la page
4. Cherchez les requêtes vers `/api/admin/dishes` et `/api/admin/orders`
5. Vérifiez le statut (devrait être 200) et la réponse

## 📊 RÉSULTATS ATTENDUS

### Si tout fonctionne :
- Logs ✅ avec des données
- Commandes affichées dans l'onglet "Commandes"
- Plats affichés dans l'onglet "Menu"

### Si échec :
- Logs ❌ avec des erreurs
- Messages d'erreur dans la notification
- Pages vides

## 🎯 PROCHAINES ÉTAPES

1. **Exécutez la procédure de debug ci-dessus**
2. **Partagez les logs complets** (surtout les erreurs)
3. **Je pourrai alors identifier le problème exact**

---

**Important** : Sans les logs de la console, il est difficile de diagnostiquer le problème. Les logs nous diront exactement ce qui échoue.