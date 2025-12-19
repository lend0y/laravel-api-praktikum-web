<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\CampaignController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/campaigns',        [CampaignController::class, 'index']);
Route::get('/campaigns/{id}',   [CampaignController::class, 'show']);
Route::post('/campaigns',       [CampaignController::class, 'store']); 

Route::middleware('auth:api')->group(function () {
    Route::post('/campaigns/{id}',  [CampaignController::class, 'update']);
    Route::delete('/campaigns/{id}', [CampaignController::class, 'destroy']);

    Route::apiResource('todos', TodoController::class);
});
