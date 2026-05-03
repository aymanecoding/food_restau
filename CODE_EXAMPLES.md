/**
 * EXEMPLES DE CODE - Utilisation de l'authentification
 * Dar EL Idrissi - Food App
 */

// ═══════════════════════════════════════════════════════════
// 1. COMPOSANT DE NAVIGATION AVEC AUTH
// ═══════════════════════════════════════════════════════════

import { useAuth } from "../hooks/useAuth";

export function NavbarWithAuth() {
  const { isAuthenticated, user, logout } = useAuth();

  return (
    <nav className="navbar">
      <div className="logo">🍽️ Dar EL Idrissi</div>
      
      <div className="nav-links">
        <a href="/">Menu</a>
        <a href="/about">À Propos</a>
        
        {isAuthenticated ? (
          <div className="user-menu">
            <span className="user-name">👋 {user.name}</span>
            <a href="/admin">Admin</a>
            <button onClick={logout} className="logout-btn">
              Déconnecter
            </button>
          </div>
        ) : (
          <div className="auth-links">
            <a href="/login">Connexion</a>
            <a href="/register" className="btn-primary">
              S'inscrire
            </a>
          </div>
        )}
      </div>
    </nav>
  );
}

// ═══════════════════════════════════════════════════════════
// 2. PAGE ADMIN PROTÉGÉE
// ═══════════════════════════════════════════════════════════

import { useAuth } from "../hooks/useAuth";
import { ordersAPI } from "../api/apiClient";

export function AdminPage() {
  const { isAuthenticated, user, loading } = useAuth();
  const [orders, setOrders] = React.useState([]);

  React.useEffect(() => {
    loadOrders();
  }, []);

  async function loadOrders() {
    try {
      const data = await ordersAPI.getAll();
      setOrders(data);
    } catch (err) {
      console.error("Erreur:", err);
    }
  }

  // Attendre le chargement de l'auth
  if (loading) {
    return <LoadingSpinner />;
  }

  // Non authentifié → afficher login
  if (!isAuthenticated) {
    return <LoginForm />;
  }

  // Authentifié → afficher admin
  return (
    <div className="admin-panel">
      <h1>😀 Bienvenue {user.name}</h1>
      <p>Email: {user.email}</p>
      
      <section className="orders-section">
        <h2>Commandes ({orders.length})</h2>
        {orders.map(order => (
          <OrderCard key={order.id} order={order} />
        ))}
      </section>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════
// 3. FORMULAIRE DE CRÉATION DE PLAT (ADMINONLY)
// ═══════════════════════════════════════════════════════════

import { dishesAPI } from "../api/apiClient";
import { useAuth } from "../hooks/useAuth";

export function CreateDishForm() {
  const { isAuthenticated } = useAuth();
  const [formData, setFormData] = React.useState({
    name: "",
    description: "",
    price: 0,
    category_id: 1,
  });

  async function handleSubmit(e) {
    e.preventDefault();

    try {
      const dish = await dishesAPI.create(formData);
      console.log("✅ Plat créé:", dish);
      setFormData({ name: "", description: "", price: 0, category_id: 1 });
    } catch (err) {
      console.error("❌ Erreur:", err.message);
    }
  }

  if (!isAuthenticated) {
    return <p>Vous devez être connecté</p>;
  }

  return (
    <form onSubmit={handleSubmit}>
      <input
        type="text"
        placeholder="Nom du plat"
        value={formData.name}
        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
        required
      />

      <textarea
        placeholder="Description"
        value={formData.description}
        onChange={(e) =>
          setFormData({ ...formData, description: e.target.value })
        }
      />

      <input
        type="number"
        placeholder="Prix"
        value={formData.price}
        onChange={(e) => setFormData({ ...formData, price: e.target.value })}
        required
      />

      <button type="submit">Créer Plat</button>
    </form>
  );
}

// ═══════════════════════════════════════════════════════════
// 4. GESTION DES COMMANDES (PROTÉGÉ)
// ═══════════════════════════════════════════════════════════

import { ordersAPI } from "../api/apiClient";
import { useAuth } from "../hooks/useAuth";

export function OrderManager() {
  const { isAuthenticated } = useAuth();
  const [orders, setOrders] = React.useState([]);

  async function loadOrders() {
    try {
      const data = await ordersAPI.getAll();
      setOrders(data);
    } catch (err) {
      if (err.message.includes("401")) {
        // Token expiré, user sera auto-redirigé vers login
        console.log("Session expirée");
      }
    }
  }

  async function updateOrderStatus(orderId, newStatus) {
    try {
      const updated = await ordersAPI.update(orderId, {
        status: newStatus,
      });
      
      // Mettre à jour l'interface
      setOrders(
        orders.map((order) =>
          order.id === orderId ? updated : order
        )
      );
      
      console.log("✅ Commande mise à jour");
    } catch (err) {
      console.error("❌ Erreur:", err.message);
    }
  }

  async function deleteOrder(orderId) {
    try {
      await ordersAPI.delete(orderId);
      setOrders(orders.filter((order) => order.id !== orderId));
      console.log("✅ Commande supprimée");
    } catch (err) {
      console.error("❌ Erreur:", err.message);
    }
  }

  if (!isAuthenticated) {
    return <p>Authentification requise</p>;
  }

  return (
    <div className="order-manager">
      {orders.map((order) => (
        <div key={order.id} className="order-card">
          <h3>Commande #{order.id}</h3>
          <p>Client: {order.client_name}</p>
          
          <select
            value={order.status}
            onChange={(e) =>
              updateOrderStatus(order.id, e.target.value)
            }
          >
            <option value="pending">Nouvelle</option>
            <option value="confirmed">Confirmée</option>
            <option value="preparing">En préparation</option>
            <option value="ready">Prête</option>
            <option value="delivered">Livrée</option>
          </select>
          
          <button onClick={() => deleteOrder(order.id)}>
            Supprimer
          </button>
        </div>
      ))}
    </div>
  );
}

// ═══════════════════════════════════════════════════════════
// 5. ERREUR HANDLING AVANCÉ
// ═══════════════════════════════════════════════════════════

export function ApiCallWithErrorHandling() {
  const [error, setError] = React.useState(null);
  const [loading, setLoading] = React.useState(false);

  async function fetchData() {
    setLoading(true);
    setError(null);

    try {
      const orders = await ordersAPI.getAll();
      console.log("✅ Données reçues:", orders);
    } catch (err) {
      // Différents types d'erreurs
      if (err.message.includes("401")) {
        setError("Votre session a expiré. Reconnexion...");
        // User sera auto-redirigé par apiClient.js
      } else if (err.message.includes("403")) {
        setError("Vous n'avez pas les permissions");
      } else if (err.message.includes("404")) {
        setError("Ressource non trouvée");
      } else {
        setError(err.message || "Erreur lors du chargement");
      }
    } finally {
      setLoading(false);
    }
  }

  return (
    <div>
      {error && <div className="error-alert">⚠️ {error}</div>}
      
      <button onClick={fetchData} disabled={loading}>
        {loading ? "Chargement..." : "Charger Données"}
      </button>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════
// 6. CALLBACK DE REDIRECTION APRÈS LOGIN
// ═══════════════════════════════════════════════════════════

export function LoginPageWithRedirect() {
  const navigate = useNavigate(); // Si using React Router
  const { login } = useAuth();
  const [isLoading, setIsLoading] = React.useState(false);

  async function handleLogin(email, password) {
    setIsLoading(true);
    
    try {
      await login(email, password);
      
      // Redirection automatique ou manuelle
      navigate("/admin"); // React Router
      // ou setPage("admin"); // Custom routing
      
      console.log("✅ Connecté et redirigé");
    } catch (err) {
      console.error("❌ Erreur login:", err.message);
    } finally {
      setIsLoading(false);
    }
  }

  return (
    <LoginForm onSuccess={() => navigate("/admin")} />
  );
}

// ═══════════════════════════════════════════════════════════
// 7. STOCKER DONNÉES UTILISATEUR
// ═══════════════════════════════════════════════════════════

export function UserProfile() {
  const { user, token } = useAuth();

  React.useEffect(() => {
    if (user) {
      // Stocker les données de l'utilisateur
      console.log("User ID:", user.id);
      console.log("User Email:", user.email);
      console.log("User Name:", user.name);
      
      // Utiliser pour des requêtes spécifiques
      localStorage.setItem("currentUserId", user.id);
    }
  }, [user]);

  return (
    <div className="profile">
      <h1>{user?.name}</h1>
      <p>Email: {user?.email}</p>
      <p>ID: {user?.id}</p>
      <p className="token-info">Token présent: {token ? "✅" : "❌"}</p>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════
// 8. TESTER L'AUTH EN CONSOLE
// ═══════════════════════════════════════════════════════════

async function testAuthInConsole() {
  console.log("🧪 Test d'authentification...\n");

  // Test 1: Vérifier le token
  const token = localStorage.getItem("auth_token");
  console.log("1. Token stocké:", token ? "✅ Oui" : "❌ Non");

  // Test 2: Vérifier l'utilisateur
  const user = JSON.parse(localStorage.getItem("auth_user"));
  console.log("2. Utilisateur:", user ? `✅ ${user.name}` : "❌ Non");

  // Test 3: Faire un appel API protégé
  try {
    const orders = await ordersAPI.getAll();
    console.log("3. API Protégée:", `✅ ${orders.length} commandes`);
  } catch (err) {
    console.log("3. API Protégée:", `❌ ${err.message}`);
  }

  // Test 4: Vérifier les routes publiques
  try {
    const dishes = await fetch(
      "http://localhost:8000/api/dishes"
    ).then((r) => r.json());
    console.log("4. API Publique:", `✅ ${dishes.length} plats`);
  } catch (err) {
    console.log("4. API Publique:", `❌ ${err.message}`);
  }

  console.log("\n✅ Tests terminés!");
}

// Pour exécuter dans la console browser:
// testAuthInConsole()

export { testAuthInConsole };
