<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GuessController;
use App\Http\Controllers\LeaderboardController;

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Game routes
    // Room management
    Route::post('/rooms', [RoomController::class, 'store']);
    Route::post('/rooms/{room}/join', [RoomController::class, 'join']);
    Route::get('/rooms/{room}', [RoomController::class, 'show']);
    Route::post('/rooms/{room}/secret', [RoomController::class, 'setSecretNumber']);
    Route::get('/rooms/{room}/history', [RoomController::class, 'getGameHistory']);
    Route::get('/rooms/{room}/guesses', [GuessController::class, 'getRoomGuesses']);
    
    // Gameplay
    Route::post('/rooms/{room}/guess', [GuessController::class, 'submit']);
    
    // Global history
    Route::get('/history', [RoomController::class, 'getAllGameHistory']);
    
    // Legacy routes (keeping for compatibility)
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
});

// Helper to find room by code
Route::post('/rooms/find', [RoomController::class, 'findByCode']);
