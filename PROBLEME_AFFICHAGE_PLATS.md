# 🔍 PROBLÈME D'AFFICHAGE DES ENTRÉES ET DESSERTS

## 📊 ANALYSE

### Ce qui a été trouvé dans la base de données :
- ✅ **4 catégories** : Entrées (ID:1), Plats principaux (ID:2), Desserts (ID:3), Boissons (ID:4)
- ✅ **14 plats** répartis dans toutes les catégories :
  - 4 entrées (ID: 1,2,3,15)
  - 5 plats principaux (ID: 4,5,6,7,8)
  - 2 desserts (ID: 9,10)
  - 3 boissons (ID: 11,12,13)

### Ce que le frontend attend :
Dans `frontend/src/data/constants.js`, les catégories sont définies ainsi :
```javascript
export const CATEGORIES = [
  { id: 1, name: "Entrées",  icon: Salad,    order: 1 },
  { id: 2, name: "Plats",    icon: Utensils, order: 2 },  // ⚠️ "Plats" vs "Plats principaux"
  { id: 3, name: "Desserts", icon: Cookie,   order: 3 },
  { id: 4, name: "Boissons", icon: Coffee,   order: 4 },
];
```

### Problème identifié :
La catégorie dans la base de données s'appelle **"Plats principaux"** mais le frontend s'attend à **"Plats"**.

Cependant, cela ne devrait pas affecter l'affichage des **Entrées** et **Desserts** qui ont des noms correspondants.

## 🔧 SOLUTIONS POSSIBLES

### Solution 1 : Vérifier le localStorage
Le frontend utilise un fallback local. Il se peut que d'anciennes données soient stockées.

**Action** :
1. Ouvrez les DevTools (F12)
2. Allez dans Application > Local Storage > http://localhost:3000
3. Supprimez la clé `darzitoun_menu`
4. Rafraîchissez la page (F5)

### Solution 2 : Vérifier la console du navigateur
Ouvrez la console (F12) et cherchez :
- Des erreurs JavaScript
- Des messages de chargement du menu
- La valeur de `menuItems` après chargement

### Solution 3 : Tester l'API directement
Exécutez cette commande pour voir les données brutes :
```bash
curl http://127.0.0.1:8000/api/dishes | ConvertFrom-Json
```

### Solution 4 : Nettoyer et recharger
1. Arrêtez les serveurs (Ctrl+C dans les deux terminaux)
2. Supprimez le localStorage du frontend
3. Redémarrez les serveurs
4. Rafraîchissez la page

## 📝 COMMANDES DE DEBUG

### Vérifier les plats depuis le backend :
```bash
php backend/check_dishes.php
```

### Tester l'API depuis le navigateur :
Ouvrez cette URL : http://127.0.0.1:8000/api/dishes

### Inspecter le menu dans le frontend :
Dans la console du navigateur (F12), exécutez :
```javascript
// Après chargement de la page
console.log("Menu items:", window.localStorage.getItem('darzitoun_menu'));
```

## 🎯 PROCHAINES ÉTAPES

1. **Videz le localStorage** du frontend
2. **Rafraîchissez la page** (F5)
3. **Vérifiez la console** pour des erreurs
4. **Dites-moi** ce que vous voyez :
   - Les catégories s'affichent-elles ?
   - Y a-t-il des erreurs dans la console ?
   - Le nombre de plats affichés est-il correct ?

---

**Note** : Les données sont bien présentes dans la base de données. Le problème est probablement lié au chargement ou au filtrage dans le frontend.