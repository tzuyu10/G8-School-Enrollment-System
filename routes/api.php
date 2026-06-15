<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// ─── Public routes ───────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ─── Authenticated routes ────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── Lookup routes (all authenticated roles) ──────────────
    Route::prefix('lookup')->group(function () {
        Route::get('/semesters',   [LookupController::class, 'semesters']);
        Route::get('/colleges',    [LookupController::class, 'colleges']);
        Route::get('/programs',    [LookupController::class, 'programs']);
        Route::get('/year-levels', [LookupController::class, 'yearLevels']);
        Route::get('/sections',    [LookupController::class, 'sections']);
        Route::get('/subjects',    [LookupController::class, 'subjects']);
    });

    // ── Student routes ───────────────────────────────────────
    Route::middleware('role:student')->group(function () {
        Route::post('/enrollment',        [EnrollmentController::class, 'submit']);
        Route::get('/enrollment/status',  [EnrollmentController::class, 'status']);
    });

    // ── Registrar routes ─────────────────────────────────────
    Route::middleware('role:registrar,admin')->group(function () {
        Route::get('/applications',              [EnrollmentController::class, 'index']);
        Route::put('/applications/{id}/approve', [EnrollmentController::class, 'approve']);
        Route::put('/applications/{id}/reject',  [EnrollmentController::class, 'reject']);
    });

    // ── Admin routes ─────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/users',         [UserController::class, 'index']);
        Route::post('/users',        [UserController::class, 'store']);
        Route::put('/users/{id}',    [UserController::class, 'update']);
    });

});