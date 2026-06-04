<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\UploadController;

// ── Public Routes (no auth required) ───────────────────────
Route::post('/login', [AuthController::class, 'login']);

// Public read endpoints (website fetches these)
Route::get('/articles',         [ArticleController::class, 'index']);
Route::get('/articles/{id}',    [ArticleController::class, 'show']);

Route::get('/blogs',            [BlogController::class, 'index']);
Route::get('/blogs/{id}',       [BlogController::class, 'show']);

Route::get('/interviews',            [InterviewController::class, 'index']);
Route::get('/interviews/{slug}',     [InterviewController::class, 'show']);

Route::get('/people',           [PersonController::class, 'index']);
Route::get('/people/{id}',      [PersonController::class, 'show']);

Route::get('/settings',         [SettingController::class, 'index']);
Route::get('/stats',            [StatsController::class, 'index']);

// ── Protected Routes (dashboard — requires admin token) ─────
Route::middleware('admin.token')->group(function () {

    // Articles
    Route::post('/articles',        [ArticleController::class, 'store']);
    Route::put('/articles/{id}',    [ArticleController::class, 'update']);
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);

    // Blogs
    Route::post('/blogs',           [BlogController::class, 'store']);
    Route::put('/blogs/{id}',       [BlogController::class, 'update']);
    Route::delete('/blogs/{id}',    [BlogController::class, 'destroy']);

    // Interviews
    Route::post('/interviews',           [InterviewController::class, 'store']);
    Route::put('/interviews/{id}',       [InterviewController::class, 'update']);
    Route::delete('/interviews/{id}',    [InterviewController::class, 'destroy']);

    // People
    Route::post('/people',          [PersonController::class, 'store']);
    Route::put('/people/{id}',      [PersonController::class, 'update']);
    Route::delete('/people/{id}',   [PersonController::class, 'destroy']);

    // Settings
    Route::put('/settings',         [SettingController::class, 'update']);

    // Upload
    Route::post('/upload',          [UploadController::class, 'store']);
});
