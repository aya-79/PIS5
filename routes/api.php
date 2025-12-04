
<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardEnseignantController;
use App\Http\Controllers\Api\PointageController;


// -------------------------------
// Routes existantes de Breeze
// -------------------------------

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', function () {
    return response()->json(['message' => 'API fonctionne']);
});

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/me', [AuthController::class, 'me']);                 // infos utilisateur connecté
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    // Dashboard Enseignant
    Route::get('/dashboard-enseignant', [DashboardEnseignantController::class, 'index']);

    // Statistiques de l'utilisateur
    Route::get('/statistics', [AuthController::class, 'statistics']);

     Route::prefix('pointages')->group(function () {
        Route::get('/', [PointageController::class, 'index']);
        Route::post('/', [PointageController::class, 'store']);
        Route::get('/{id}', [PointageController::class, 'show']);
        Route::put('/{id}', [PointageController::class, 'update']);
        Route::delete('/{id}', [PointageController::class, 'destroy']);
        
        // Validation (pour administrateurs)
        Route::post('/{id}/valider', [PointageController::class, 'valider']);
    });
});