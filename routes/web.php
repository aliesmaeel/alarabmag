<?php

use Illuminate\Support\Facades\Route;

// ── Public Arabic Magazine Website ─────────────────────────
Route::get('/', function () {
    $file = public_path('site/index.html');
    if (file_exists($file)) {
        return response()->file($file);
    }
    return response('<h1>Website file not found. Place index.html in public/site/</h1>', 404);
});

// ── Admin Dashboard ─────────────────────────────────────────
// Mounted by Filament's AdminPanelProvider at /dashboard.
