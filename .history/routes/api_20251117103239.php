<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\EtatCivilController;

Route::post('/login/s01', [LoginController::class, 'loginS01']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'service.s01'])->prefix('etat-civil')->group(function () {
    Route::get('/actes', [EtatCivilController::class, 'index']);
    Route::get('/actes/{acte}', [EtatCivilController::class, 'show']);
    Route::post('/actes', [EtatCivilController::class, 'store']);
    Route::put('/actes/{acte}', [EtatCivilController::class, 'update']);
    Route::post('/actes/{acte}/valider', [EtatCivilController::class, 'valider']);
    Route::get('/certificats/{type}', [EtatCivilController::class, 'genererCertificat']);
    Route::get('/recherche', [EtatCivilController::class, 'recherche']);
});
