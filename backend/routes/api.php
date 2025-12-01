<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EnseignantAuthController;

// -------------------------------
// Routes existantes de Breeze
// -------------------------------
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// -------------------------------
// Routes API pour les enseignants
// -------------------------------
Route::prefix('enseignant')->group(function () {
    Route::post('register', [EnseignantAuthController::class, 'register']);
    Route::post('login', [EnseignantAuthController::class, 'login']);

    // Tu pourras ajouter plus tard :
    // Route::middleware('auth:sanctum')->post('logout', [EnseignantAuthController::class, 'logout']);
    // Route::middleware('auth:sanctum')->post('reset-password', [EnseignantAuthController::class, 'resetPassword']);
});
