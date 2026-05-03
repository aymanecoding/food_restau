<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AdminAuthController extends Controller
{
    /**
     * Connexion administrateur
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Trouver l'utilisateur dans la base de données par email
        $user = User::where('email', $request->email)->first();

        // Vérifier si l'utilisateur existe et si le mot de passe correspond
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects.',
            ], 401);
        }

        // Vérifier que l'utilisateur est un administrateur
        if (!$user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Accès administrateur refusé.',
            ], 403);
        }

        // Créer un token Sanctum pour l'admin
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

    /**
     * Vérifier si l'admin est connecté
     */
    public function check(Request $request)
    {
        \Log::info('Admin check called', [
            'auth_check' => \Auth::guard('sanctum')->check(),
            'user' => \Auth::guard('sanctum')->user(),
        ]);

        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non connecté',
            ], 401);
        }

        $user = Auth::guard('sanctum')->user();
        return response()->json([
            'success' => true,
            'message' => 'Connecté',
            'admin' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Déconnexion administrateur
     */
    public function logout(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * Obtenir la liste des admins
     */
    public function getAdmins(Request $request)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 401);
        }

        $admins = User::all();
        return response()->json([
            'success' => true,
            'admins' => $admins,
        ]);
    }

    /**
     * Ajouter un nouvel admin
     */
    public function addAdmin(Request $request)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin ajouté avec succès',
            'admin' => $admin,
        ]);
    }
}
