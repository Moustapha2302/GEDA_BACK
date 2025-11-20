<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\EtatCivilController;

// ====================== AUTH ======================
Route::post('/login/s01', [LoginController::class, 'loginS01']);
Route::post('/login/maire', [LoginController::class, 'loginMaire']);
Route::post('/login/sg', [LoginController::class, 'loginSG']);
Route::post('/login/agent-s01', [LoginController::class, 'loginAgentS01']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// ====================== SERVICE ÉTAT CIVIL (S01) ======================
Route::middleware(['auth:sanctum', 'service:S01'])  // ✅ Correct
    ->prefix('etat-civil')
    ->name('etat-civil.')
    ->group(function () {
        Route::get('/actes', [EtatCivilController::class, 'index']);
        Route::get('/actes/{acte}', [EtatCivilController::class, 'show']);
        Route::post('/actes', [EtatCivilController::class, 'store']);
        Route::put('/actes/{acte}', [EtatCivilController::class, 'update']);
        Route::post('/actes/{acte}/valider', [EtatCivilController::class, 'valider']);
        Route::get('/certificats/{type}', [EtatCivilController::class, 'genererCertificat']);
        Route::get('/recherche', [EtatCivilController::class, 'recherche']);
    });

// ====================== AUTH FINANCE (S02) ======================
// Connexions spécifiques par rôle
Route::post('/login/controleur-financier', [LoginController::class, 'loginControleurFinancier']);
Route::post('/login/chef-s02', [LoginController::class, 'loginChefS02']);
Route::post('/login/agent-s02', [LoginController::class, 'loginAgentS02']);
Route::post('/login/services-techniques', [LoginController::class, 'loginServicesTechniques']);
Route::post('/login/autres-services', [LoginController::class, 'loginAutresServices']);

// Connexion générique S02 (SG, Maire, Chef, Agents)
Route::post('/login/s02', [LoginController::class, 'loginS02']);

// ====================== SERVICE FINANCE (S02) ======================
Route::middleware(['auth:sanctum', 'service:S02'])
    ->prefix('finance')
    ->name('finance.')
    ->group(function () {
        // CRUD Documents financiers (Chef + Agents S02)
        Route::get('/documents', [FinancesController::class, 'index']);
        Route::get('/documents/{document}', [FinanceController::class, 'show']);
        Route::post('/documents', [FinanceController::class, 'store']);
        Route::put('/documents/{document}', [FinanceController::class, 'update']);

        // Validation (Chef S02, SG uniquement)
        Route::post('/documents/{document}/valider', [FinanceController::class, 'valider']);

        // Recherche (tous les utilisateurs authentifiés S02)
        Route::get('/recherche', [FinanceController::class, 'recherche']);

        // Statistiques et Rapports (Chef S02, SG, Contrôleur Financier)
        Route::get('/statistiques', [FinanceController::class, 'statistiques']);
        Route::get('/rapport-mensuel', [FinanceController::class, 'rapportMensuel']);
    });

// ====================== ACCÈS LECTURE POUR AUTRES SERVICES ======================
// Services techniques et autres services : lecture seule
Route::middleware(['auth:sanctum'])
    ->prefix('finance')
    ->group(function () {
        // Consultation uniquement (pas de création/modification)
        Route::get('/consultation/documents', [FinanceController::class, 'index']);
        Route::get('/consultation/documents/{document}', [FinanceController::class, 'show']);
        Route::get('/consultation/recherche', [FinanceController::class, 'recherche']);
    });
