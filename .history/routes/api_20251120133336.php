<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\EtatCivilController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\UrbanismeController;


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

//* API Routes - Service Finance (S02)

//

// ====================== AUTHENTIFICATION ======================

// Login Contrôleur Financier
Route::post('/login/controleur-financier', [LoginController::class, 'loginControleurFinancier']);

// Login Chef S02
Route::post('/login/chef-s02', [LoginController::class, 'loginChefS02']);

// Login Agent S02 (Fatou Sané et autres agents)
Route::post('/login/agent-s02', [LoginController::class, 'loginAgentS02']);

// Login générique S02 (SG, Maire, et autres autorisés)
Route::post('/login/s02', [LoginController::class, 'loginS02']);

// Login Services Techniques (consultation uniquement)
Route::post('/login/services-techniques', [LoginController::class, 'loginServicesTechniques']);

// Login Autres Services (consultation uniquement)
Route::post('/login/autres-services', [LoginController::class, 'loginAutresServices']);

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// ====================== BUDGETS ======================

Route::middleware(['auth:sanctum'])->prefix('finances')->group(function () {

    // Liste des budgets (Contrôleur Financier, Chef S02, SG, Maire)
    Route::get('/budgets', [FinanceController::class, 'getBudgets']);

    // Budget d'un service spécifique
    Route::get('/budgets/{service}', [FinanceController::class, 'getBudgetService']);
});

// ====================== PIÈCES COMPTABLES ======================

Route::middleware(['auth:sanctum'])->prefix('finances')->group(function () {

    // Liste des pièces comptables
    Route::get('/pieces-comptables', [FinanceController::class, 'index']);

    // Détail d'une pièce comptable
    Route::get('/pieces-comptables/{id}', [FinanceController::class, 'show']);

    // Créer une pièce comptable (Agent S02, Chef S02)
    Route::post('/pieces-comptables', [FinanceController::class, 'store']);

    // Modifier une pièce comptable (Agent S02 propriétaire, Chef S02)
    Route::put('/pieces-comptables/{id}', [FinanceController::class, 'update']);

    // Valider une pièce comptable (Contrôleur Financier uniquement)
    Route::post('/pieces-comptables/{id}/valider', [FinanceController::class, 'valider']);
});

// ====================== BONS DE COMMANDE ======================

Route::middleware(['auth:sanctum'])->prefix('finances')->group(function () {

    // Liste des bons de commande
    Route::get('/bons-commande', [FinanceController::class, 'getBonsCommande']);

    // Créer un bon de commande (tous les services)
    Route::post('/bons-commande', [FinanceController::class, 'createBonCommande']);

    // Viser un bon de commande (Contrôleur Financier uniquement)
    Route::post('/bons-commande/{id}/viser', [FinanceController::class, 'viserBonCommande']);
});

// ====================== MARCHÉS PUBLICS ======================

Route::middleware(['auth:sanctum'])->prefix('finances')->group(function () {

    // Liste des marchés publics
    Route::get('/marches-publics', [FinanceController::class, 'getMarchesPublics']);

    // Viser un marché public (Contrôleur Financier uniquement)
    Route::post('/marches-publics/{id}/viser', [FinanceController::class, 'viserMarchePublic']);
});

// ====================== RAPPORTS ======================

Route::middleware(['auth:sanctum'])->prefix('finances')->group(function () {

    // Rapport budgétaire
    Route::get('/rapports/budget', [FinanceController::class, 'rapportBudget']);
});

// ====================== RECHERCHE ======================

Route::middleware(['auth:sanctum'])->prefix('finances')->group(function () {

    // Recherche
    Route::get('/recherche', [FinanceController::class, 'recherche']);
});

// ====================== STATISTIQUES ======================

Route::middleware(['auth:sanctum'])->prefix('finances')->group(function () {

    // Statistiques
    Route::get('/statistiques', [FinanceController::class, 'statistiques']);
});

// ====================== CONSULTATION (Autres Services) ======================

Route::middleware(['auth:sanctum'])->prefix('finances/consultation')->group(function () {

    // Les autres services peuvent seulement consulter
    Route::get('/documents', [FinanceController::class, 'index']);
    Route::get('/documents/{id}', [FinanceController::class, 'show']);
    Route::get('/recherche', [FinanceController::class, 'recherche']);
});

// ====================== AUTHENTIFICATION S03 ======================
Route::post('/login/chef-s03', [LoginController::class, 'loginChefS03']);
Route::post('/login/agent-s03', [LoginController::class, 'loginAgentS03']);

Route::middleware(['auth:sanctum'])->prefix('urbanisme')->group(function () {

    // Permis de construire
    Route::get('/permis-construire', [UrbanismeController::class, 'indexPermis']);
    Route::post('/permis-construire', [UrbanismeController::class, 'storePermis']);
    Route::get('/permis-construire/{id}', [UrbanismeController::class, 'showPermis']);
    Route::put('/permis-construire/{id}', [UrbanismeController::class, 'updatePermis']);
    Route::post('/permis-construire/{id}/avis-juridique', [UrbanismeController::class, 'demanderAvisJuridique']);
    Route::post('/permis-construire/{id}/valider', [UrbanismeController::class, 'validerPermis']);

    // Plans d’occupation des sols
    Route::get('/plans-occupation', [UrbanismeController::class, 'indexPlans']);
    Route::post('/plans-occupation', [UrbanismeController::class, 'storePlan']);

    // Autorisations diverses
    Route::get('/autorisations', [UrbanismeController::class, 'indexAutorisations']);

    // Suivi dossier citoyen
    Route::get('/dossiers/citoyen/{id}', [UrbanismeController::class, 'suiviDossierCitoyen']);
});
