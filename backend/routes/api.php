<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes d'authentification client (publiques)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/revoke-all-tokens', [AuthController::class, 'revokeAllTokens']);
});

// Routes d'authentification admin (publiques)
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::middleware('admin.auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/check', [AdminAuthController::class, 'check']);
        Route::get('/admins', [AdminAuthController::class, 'getAdmins']);
        Route::post('/add-admin', [AdminAuthController::class, 'addAdmin']);
    });
});

// Routes publiques (menu client)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/dishes', [DishController::class, 'index']);
Route::get('/dishes/{id}', [DishController::class, 'show']);

// Test diagnostique
Route::post('/test-order', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Route de test en fonctionnement',
        'received_data' => $request->all()
    ]);
});

// Routes client (commandes) - Publiques pour permettre aux clients non connectés de commander
Route::post('/orders', [OrderController::class, 'store']);

// Routes client protégées pour la consultation des commandes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'myOrders']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);
});

// Routes admin pour les commandes (index, show, update, destroy)
Route::middleware('admin.auth')->prefix('admin')->group(function () {
    // Gestion des plats
    Route::get('/dishes', [DishController::class, 'index']);
    Route::post('/dishes', [DishController::class, 'store']);
    Route::get('/dishes/{id}', [DishController::class, 'show']);
    Route::put('/dishes/{id}', [DishController::class, 'update']);
    Route::delete('/dishes/{id}', [DishController::class, 'destroy']);

    // Gestion des commandes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    // Gestion des catégories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});