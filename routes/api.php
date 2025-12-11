<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\CampaignController;

// =========================
// AUTH (PUBLIC)
// =========================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// =========================
// AUTH (PROTECTED)
// =========================
Route::middleware('auth:api')->group(function () {
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// =========================
// CAMPAIGNS
// =========================

// Public Routes
Route::get('/campaigns', [CampaignController::class, 'index']);
Route::get('/campaigns/{id}', [CampaignController::class, 'show']);

// Protected Routes
Route::middleware('auth:api')->group(function () {
    Route::post('/campaigns', [CampaignController::class, 'store']);
    Route::patch('/campaigns/{id}', [CampaignController::class, 'update']);
    Route::delete('/campaigns/{id}', [CampaignController::class, 'destroy']);

    // TODOS PROTECTED (WAJIB SESUAI MODUL 6)
    Route::apiResource('todos', TodoController::class);
});
