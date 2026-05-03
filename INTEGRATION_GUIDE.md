/** 
 * INTEGRATION GUIDE - Authentication with Existing App
 * Dar EL Idrissi Food App 
 */

/**
 * ─────────────────────────────────────────────────────────
 * OPTION 1: Adapter App.js pour utiliser routes (Recommandé)
 * ─────────────────────────────────────────────────────────
 * 
 * Installer React Router:
 * npm install react-router-dom
 * 
 * Puis mettre à jour App.js:
 */

import { BrowserRouter as Router, Routes, Route, Navigate } from "react-router-dom";
import { useAuth } from "./hooks/useAuth";
import LoginForm from "./components/LoginForm";
import RegisterForm from "./components/RegisterForm";
import ProtectedRoute from "./components/ProtectedRoute";
import HomePage from "./pages/HomePage";
import AdminDashboard from "./admin/AdminDashboard";

// VERSION 1 - Avec React Router
function AppWithRouter() {
  const { isAuthenticated, loading } = useAuth();

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-500 mx-auto mb-4"></div>
          <p className="text-stone-600">Chargement...</p>
        </div>
      </div>
    );
  }

  return (
    <Router>
      <Routes>
        {/* Routes publiques */}
        <Route path="/" element={<HomePage />} />
        <Route path="/login" element={<LoginForm />} />
        <Route path="/register" element={<RegisterForm />} />

        {/* Routes protégées */}
        <Route
          path="/admin"
          element={
            <ProtectedRoute>
              <AdminDashboard />
            </ProtectedRoute>
          }
        />

        {/* Redirection par défaut */}
        <Route path="*" element={<Navigate to="/" />} />
      </Routes>
    </Router>
  );
}

export default AppWithRouter;

/**
 * ─────────────────────────────────────────────────────────
 * OPTION 2: Garder le système actuel (Sans React Router)
 * ─────────────────────────────────────────────────────────
 * 
 * Si tu veux garder le système de pages actuel,
 * adapter App.js comme ceci:
 */

import { useAuth } from "./hooks/useAuth";
import LoginForm from "./components/LoginForm";
import RegisterForm from "./components/RegisterForm";

function AppWithCustomRouting() {
  const [page, setPage] = useState("home");
  const { isAuthenticated, loading, logout } = useAuth();

  // Rediriger vers login si page admin sans authentification
  if ((page === "admin" || page === "admin-login") && !isAuthenticated && !loading) {
    return (
      <div className="min-h-screen">
        {page === "admin-login" && (
          <LoginForm onSuccess={() => setPage("admin")} />
        )}
      </div>
    );
  }

  if (page === "login") {
    return <LoginForm onSuccess={() => setPage("home")} />;
  }

  if (page === "register") {
    return <RegisterForm onSuccess={() => setPage("home")} />;
  }

  // ... rest du code existant
}

/**
 * ─────────────────────────────────────────────────────────
 * UTILISER useAuth DANS UN COMPOSANT
 * ─────────────────────────────────────────────────────────
 */

import { useAuth } from "./hooks/useAuth";

function AdminDashboardWithAuth() {
  const { user, isAuthenticated, loading, logout } = useAuth();

  if (loading) {
    return <div>Chargement...</div>;
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" />;
  }

  return (
    <div>
      <header>
        <h1>Admin Dashboard</h1>
        <p>Bienvenue {user.name}!</p>
        <button onClick={logout}>Déconnecter</button>
      </header>
      {/* Reste du contenu */}
    </div>
  );
}

/**
 * ─────────────────────────────────────────────────────────
 * APPELS API AVEC AUTHENTIFICATION
 * ─────────────────────────────────────────────────────────
 * 
 * AVANT (sans authentification):
 */

// ❌ AVANT
async function loadOrders() {
  const response = await fetch("http://localhost:8000/api/orders");
  const data = await response.json();
}

// ✅ APRÈS (avec authentification automatique)
import { ordersAPI } from "./api/apiClient";

async function loadOrders() {
  const data = await ordersAPI.getAll(); // Token inclus automatiquement!
}

// Pour les autres opérations:
export async function createOrder(orderData) {
  return await ordersAPI.create(orderData); // Authentifié
}

export async function updateOrder(id, status) {
  return await ordersAPI.update(id, { status }); // Authentifié
}

export async function createDish(dishData) {
  return await dishesAPI.create(dishData); // Authentifié
}

/**
 * ─────────────────────────────────────────────────────────
 * VÉRIFIER QUE LES ROUTES PUBLIQUES RESTENT ACCESSIBLES
 * ─────────────────────────────────────────────────────────
 * 
 * Ces appels n'ont pas besoin de token:
 */

// ✅ PUBLIQUES (pas de token nécessaire)
import { dishesAPI } from "./api/apiClient";

export async function getPublicDishes() {
  // Cette route peut être appelée sans authentification
  return await dishesAPI.getAll();
}

/**
 * ─────────────────────────────────────────────────────────
 * CRÉER UN UTILISATEUR DE TEST
 * ─────────────────────────────────────────────────────────
 * 
 * Dans le terminal backend:
 */

// php artisan tinker
// >>> User::create([
// ...   'name' => 'Admin Test',
// ...   'email' => 'admin@test.com',
// ...   'password' => bcrypt('password123')
// ... ])

/**
 * ─────────────────────────────────────────────────────────
 * FLUX D'UTILISATION COMPLET
 * ─────────────────────────────────────────────────────────ß
 * 
 * 1. Utilisateur clique "Connexion"
 * 2. Formulaire LoginForm s'affiche
 * 3. Utilisateur entre email + password
 * 4. Hook useAuth appelle authAPI.login()
 * 5. Backend retourne token + user
 * 6. Token sauvegardé dans localStorage
 * 7. useAuth met à jour le state (user, isAuthenticated)
 * 8. App redirige vers dashboard
 * 9. Tous les appels API incluent le Bearer token auto
 * 
 * À la déconnexion:
 * 1. Utilisateur clique "Déconnecter"
 * 2. Hook useAuth appelle authAPI.logout()
 * 3. Backend révoque le token
 * 4. Token supprimé du localStorage
 * 5. useAuth remet user à null
 * 6. App redirige vers home
 */

/**
 * ─────────────────────────────────────────────────────────
 * GESTION DES ERREURS
 * ─────────────────────────────────────────────────────────
 */

function MyComponent() {
  const { isAuthenticated, error } = useAuth();

  return (
    <div>
      {error && (
        <div className="error-banner">
          Erreur: {error}
        </div>
      )}
      {!isAuthenticated && (
        <p>Veuillez vous connecter</p>
      )}
    </div>
  );
}

// Dans un appel API:
async function loadData() {
  try {
    const data = await ordersAPI.getAll();
    // ...
  } catch (err) {
    console.error("Erreur:", err.message);
    // Le user est auto-redirigé vers /login en cas d'erreur 401
  }
}

/**
 * ─────────────────────────────────────────────────────────
 * CHECKLIST DE DÉPLOIEMENT
 * ─────────────────────────────────────────────────────────
 * 
 * Backend:
 * ☐ Migration hasApiTokens sur User model ✅ (déjà fait)
 * ☐ AuthController créé ✅ (déjà fait)
 * ☐ Routes api.php configurées ✅ (déjà fait)
 * ☐ CORS configuré pour frontend domain
 * ☐ User test créé dans BD
 * ☐ HTTPS en production
 * ☐ Rate limiting sur login/register
 * 
 * Frontend:
 * ☐ apiClient.js créé ✅ (déjà fait)
 * ☐ useAuth hook créé ✅ (déjà fait)
 * ☐ LoginForm/RegisterForm créés ✅ (déjà fait)
 * ☐ index.js wrapped avec AuthProvider ✅ (déjà fait)
 * ☐ useOrders mis à jour ✅ (déjà fait)
 * ☐ REACT_APP_API_URL configuré dans .env
 * ☐ Tous les appels API utilisent apiClient
 * 
 * Tests:
 * ☐ Tester register/login flow
 * ☐ Tester accès protected routes
 * ☐ Tester token expiration/refresh
 * ☐ Tester logout
 * ☐ Tester routes publiques restent accessibles
 */

export { AdminDashboardWithAuth };
