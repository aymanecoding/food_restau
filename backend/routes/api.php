<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\OrderController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Test diagnostique
Route::post('/test-order', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Route de test en fonctionnement',
        'received_data' => $request->all()
    ]);
});

Route::apiResource('categories', CategoryController::class);
Route::apiResource('dishes', DishController::class);
Route::apiResource('orders', OrderController::class);
