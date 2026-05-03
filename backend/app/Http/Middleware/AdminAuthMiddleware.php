<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est authentifié via Sanctum
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès administrateur requis. Veuillez vous connecter.',
            ], 401);
        }

        // Vérifier que l'utilisateur a le rôle admin
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Accès administrateur refusé. Vous n\'avez pas les permissions nécessaires.',
            ], 403);
        }

        return $next($request);
    }
}
