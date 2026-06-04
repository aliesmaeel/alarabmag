<?php

use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// ── Public Arabic Magazine Website (Blade) ─────────────────
Route::get('/', [SiteController::class, 'home'])->name('home');

Route::get('/news', [SiteController::class, 'news'])->name('news.index');
Route::get('/news/{id}', [SiteController::class, 'newsShow'])->whereNumber('id')->name('news.show');

Route::get('/blogs', [SiteController::class, 'blogs'])->name('blogs.index');
Route::get('/blogs/{id}', [SiteController::class, 'blogShow'])->whereNumber('id')->name('blogs.show');

Route::get('/doctors', [SiteController::class, 'doctors'])->name('doctors.index');
Route::get('/doctors/{id}', [SiteController::class, 'doctorShow'])->whereNumber('id')->name('doctors.show');

Route::get('/influencers', [SiteController::class, 'influencers'])->name('influencers.index');
Route::get('/influencers/{id}', [SiteController::class, 'influencerShow'])->whereNumber('id')->name('influencers.show');

Route::get('/artists', [SiteController::class, 'artists'])->name('artists.index');
Route::get('/artists/{id}', [SiteController::class, 'artistShow'])->whereNumber('id')->name('artists.show');

Route::get('/business', [SiteController::class, 'business'])->name('business.index');
Route::get('/business/{id}', [SiteController::class, 'businessShow'])->whereNumber('id')->name('business.show');

Route::get('/fashion', [SiteController::class, 'fashion'])->name('fashion.index');
Route::get('/fashion/{id}', [SiteController::class, 'fashionShow'])->whereNumber('id')->name('fashion.show');

Route::get('/interviews', [SiteController::class, 'interviews'])->name('interviews.index');
Route::get('/interviews/{interview:slug}/stream', [SiteController::class, 'interviewStream'])->name('interviews.stream');
Route::get('/interviews/{interview:slug}', [SiteController::class, 'interviewShow'])->name('interviews.show');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// ── Admin Dashboard ─────────────────────────────────────────
// Mounted by Filament's AdminPanelProvider at /dashboard.
