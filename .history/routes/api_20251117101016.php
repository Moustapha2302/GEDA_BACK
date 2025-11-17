<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\LoginController;

Route::post('/login/s01', [LoginController::class, 'loginS01']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
