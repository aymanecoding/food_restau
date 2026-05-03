#!/bin/bash
# verify_setup.sh - Vérifier que tout est bien configuré
# Run: bash verify_setup.sh

echo "═══════════════════════════════════════════════════════════"
echo "🔍 Vérification de l'installation d'authentification"
echo "═══════════════════════════════════════════════════════════"

OK="✅"
FAIL="❌"
WARN="⚠️"

# Compteurs
total=0
passed=0

# ─────────────────────────────────────────────────────────────
echo -e "\n📁 VÉRIFICATION DES FICHIERS\n"
# ─────────────────────────────────────────────────────────────

check_file() {
  local file=$1
  local description=$2
  ((total++))
  
  if [ -f "$file" ]; then
    echo "$OK $description"
    ((passed++))
  else
    echo "$FAIL $file - NOT FOUND"
  fi
}

# Backend files
check_file "backend/app/Http/Controllers/AuthController.php" "AuthController"
check_file "backend/routes/api.php" "API Routes"

# Frontend files
check_file "frontend/src/api/apiClient.js" "API Client"
check_file "frontend/src/hooks/useAuth.js" "Auth Hook"
check_file "frontend/src/components/LoginForm.jsx" "Login Form"
check_file "frontend/src/components/RegisterForm.jsx" "Register Form"
check_file "frontend/src/components/ProtectedRoute.jsx" "Protected Route"
check_file "frontend/src/styles/AuthForms.css" "Auth CSS"
check_file "frontend/src/index.js" "React Entry"
check_file "frontend/src/test-auth.js" "Frontend Tests"

# Documentation
check_file "AUTHENTICATION.md" "Auth Documentation"
check_file "INTEGRATION_GUIDE.md" "Integration Guide"
check_file "QUICK_START.md" "Quick Start"
check_file "SUMMARY.md" "Summary"
check_file "CODE_EXAMPLES.md" "Code Examples"
check_file "test_authentication.sh" "API Tests"

# ─────────────────────────────────────────────────────────────
echo -e "\n🔐 VÉRIFICATION DES CONFIGURATIONS\n"
# ─────────────────────────────────────────────────────────────

# Check AuthProvider in index.js
((total++))
if grep -q "AuthProvider" frontend/src/index.js; then
  echo "$OK AuthProvider configuré dans index.js"
  ((passed++))
else
  echo "$FAIL AuthProvider NOT found in index.js"
fi

# Check apiClient import in useOrders
((total++))
if grep -q "ordersAPI" frontend/src/hooks/useOrders.js; then
  echo "$OK useOrders utilise ordersAPI"
  ((passed++))
else
  echo "$FAIL useOrders n'utilise pas ordersAPI"
fi

# Check auth routes in api.php
((total++))
if grep -q "AuthController" backend/routes/api.php; then
  echo "$OK AuthController enregistré dans les routes"
  ((passed++))
else
  echo "$FAIL AuthController NOT found in routes"
fi

# Check Sanctum middleware
((total++))
if grep -q "auth:sanctum" backend/routes/api.php; then
  echo "$OK Middleware auth:sanctum configuré"
  ((passed++))
else
  echo "$FAIL Middleware auth:sanctum NOT found"
fi

# ─────────────────────────────────────────────────────────────
echo -e "\n🚀 VÉRIFICATION DES SERVEURS\n"
# ─────────────────────────────────────────────────────────────

# Check Laravel
((total++))
if curl -s http://localhost:8000/api/dishes > /dev/null 2>&1; then
  echo "$OK Backend Laravel est accessible"
  ((passed++))
else
  echo "$WARN Backend NOT accessible - À démarrer? (php artisan serve)"
fi

# Check React
((total++))
if curl -s http://localhost:3000 > /dev/null 2>&1; then
  echo "$OK Frontend React est accessible"
  ((passed++))
else
  echo "$WARN Frontend NOT accessible - À démarrer? (npm start)"
fi

# ─────────────────────────────────────────────────────────────
echo -e "\n👤 VÉRIFICATION DE L'UTILISATEUR TEST\n"
# ─────────────────────────────────────────────────────────────

((total++))
# Try to login (won't work if server not running)
response=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"password123"}' | grep -o "success")

if [ "$response" == "success" ]; then
  echo "$OK Utilisateur admin@test.com existe et fonctionne"
  ((passed++))
else
  echo "$WARN Impossible de tester utilisateur - Créer via: php artisan tinker"
fi

# ─────────────────────────────────────────────────────────────
echo -e "\n"
echo "═══════════════════════════════════════════════════════════"
echo "📊 RÉSULTAT: $passed/$total vérifications réussies"
echo "═══════════════════════════════════════════════════════════"

if [ $passed -eq $total ]; then
  echo -e "\n✅ TOUT EST CONFIGUÉ CORRECTEMENT!\n"
  echo "Prêt à utiliser l'authentification 🚀"
  exit 0
elif [ $passed -ge $((total - 2)) ]; then
  echo -e "\n⚠️  PRESQUE PRÊT - Vérifier les serveurs ✓\n"
  exit 1
else
  echo -e "\n❌ PROBLÈMES DÉTECTÉS - Voir les détails ci-dessus\n"
  exit 1
fi
