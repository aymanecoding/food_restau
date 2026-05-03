# ⚡ Quick Setup Guide - Authentication System

**Time needed:** 5 minutes  
**Status:** Ready to use

---

## 🚀 Step 1: Backend Setup (2 minutes)

### Create a test user:

```bash
cd backend

# Start Artisan tinker
php artisan tinker

# Create user
>>> User::create([
  'name' => 'Admin',
  'email' => 'admin@test.com', 
  'password' => bcrypt('password123')
])

# Exit tinker
>>> exit

# Start Laravel server
php artisan serve
```

**Expected:**
- Server running on `http://localhost:8000`
- Routes available (check with terminal output)

---

## 🎨 Step 2: Frontend Setup (2 minutes)

### Install dependencies:

```bash
cd frontend

# Install if not already done
npm install

# (Optional) Install React Router for newer routes
# npm install react-router-dom
```

### Create .env file:

```bash
# frontend/.env
REACT_APP_API_URL=http://localhost:8000/api
```

### Start frontend:

```bash
npm start
```

**Expected:**
- React app opens on `http://localhost:3000`
- No console errors
- AuthProvider working (visible in React DevTools)

---

## 🧪 Step 3: Test Authentication (1 minute)

### Via Frontend UI:

1. Go to `http://localhost:3000`
2. Look for login link (add to Navbar if needed)
3. Click Login
4. Enter credentials:
   - Email: `admin@test.com`
   - Password: `password123`
5. Click "Se connecter"
6. Should redirect to home (or admin dashboard)

### Via Browser Console:

```javascript
// In browser console (F12 -> Console)
localStorage.getItem('auth_token')
// Should show token like: "1|ABC123..."

localStorage.getItem('auth_user')
// Should show: {"id":1,"name":"Admin","email":"admin@test.com"}
```

### Via cURL (API test):

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"password123"}'

# Response should include token and user info

# Get orders with token
TOKEN="1|YOUR_TOKEN_HERE"
curl -X GET http://localhost:8000/api/orders \
  -H "Authorization: Bearer $TOKEN"
```

---

## 📁 File Structure

```
backend/
├── app/Http/Controllers/
│   └── AuthController.php                    [NEW]
└── routes/
    └── api.php                               [MODIFIED]

frontend/src/
├── api/
│   └── apiClient.js                          [NEW]
├── hooks/
│   ├── useAuth.js                            [NEW]
│   └── useOrders.js                          [MODIFIED]
├── components/
│   ├── LoginForm.jsx                         [NEW]
│   ├── RegisterForm.jsx                      [NEW]
│   └── ProtectedRoute.jsx                    [NEW]
├── styles/
│   └── AuthForms.css                         [NEW]
├── test-auth.js                              [NEW]
└── index.js                                  [MODIFIED]
```

---

## 🔑 Key Functions

### Login user:
```javascript
const { login } = useAuth();
await login('admin@test.com', 'password123');
```

### Get current user:
```javascript
const { user } = useAuth();
console.log(user.name); // "Admin"
```

### Make authenticated request:
```javascript
import { ordersAPI } from './api/apiClient';
const orders = await ordersAPI.getAll(); // Token auto-included!
```

### Logout:
```javascript
const { logout } = useAuth();
await logout();
```

---

## ⚠️ Troubleshooting

### Problem: "CORS error" in console

**Solution:** Backend CORS config
```bash
cd backend

# Check config/cors.php
# Add frontend URL to allowed origins:
'allowed_origins' => [
    'http://localhost:3000',
    'http://localhost:8000',
],
```

### Problem: "401 Unauthorized" after login

**Solution:** Token not being saved
```javascript
// Check in console:
localStorage.getItem('auth_token')

// If empty, check apiClient.js:
// Line: localStorage.setItem("auth_token", response.token);
```

### Problem: Page still shows login after successful auth

**Solution:** AuthProvider not wrapping app
```javascript
// Check frontend/src/index.js
import { AuthProvider } from "./hooks/useAuth";

root.render(
  <AuthProvider>
    <App />
  </AuthProvider>
);
```

### Problem: "Network error" or "Cannot fetch"

**Solution:** Servers not running
```bash
# Terminal 1 - check backend server
curl http://localhost:8000/api/dishes

# Terminal 2 - check frontend
curl http://localhost:3000

# Terminal 3 - watch backend logs
php artisan serve --verbose
```

---

## 📋 Next Steps

### 1. Add authentication to UI
- Update Navbar to show login/logout
- Hide admin button if not authenticated
- Show user name when logged in

### 2. Protect admin routes
- Wrap AdminDashboard in ProtectedRoute
- Redirect to login if not authenticated

### 3. Test complete flow
- Register new user
- Login with new credentials
- Create/edit dishes
- Create orders
- Logout

### 4. Security (Production)
- Enable HTTPS
- Use environment variables for API URL
- Add rate limiting
- Implement refresh tokens
- Use HttpOnly cookies instead of localStorage

---

## 📚 Documentation Files

- `AUTHENTICATION.md` - Complete auth system documentation
- `INTEGRATION_GUIDE.md` - How to integrate with existing app
- `test_authentication.sh` - Bash script to test API
- `frontend/src/test-auth.js` - Frontend test script

---

## ✅ Verification Checklist

- [ ] Backend server running (`php artisan serve`)
- [ ] Frontend running (npm start)
- [ ] User created in database
- [ ] Can login with credentials
- [ ] Token visible in localStorage
- [ ] Protected routes require authentication
- [ ] Public routes (menu) still accessible
- [ ] Logout removes token
- [ ] API calls include Authorization header

---

## 🎯 Success Criteria

✅ **Login form appears and accepts credentials**  
✅ **After login, user sees authenticated state**  
✅ **Token stored in localStorage**  
✅ **API calls include Bearer token**  
✅ **Logout removes token**  
✅ **Accessing protected route without auth redirects to login**  
✅ **Public routes work without authentication**  

---

**Created:** April 2026  
**Last Updated:** April 2026  
**Status:** ✅ Complete and Ready
