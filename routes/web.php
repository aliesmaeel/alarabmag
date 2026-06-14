<?php

use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;

// ── Public Arabic Magazine Website (Blade) ─────────────────
Route::get('/', [SiteController::class, 'home'])->name('home');

Route::get('/news', [SiteController::class, 'news'])->name('news.index');
Route::get('/news/{id}', [SiteController::class, 'newsRedirectFromId'])->whereNumber('id');
Route::get('/news/{article:slug}', [SiteController::class, 'newsShow'])->where('article', '.*')->name('news.show');

Route::get('/blogs', [SiteController::class, 'blogs'])->name('blogs.index');
Route::get('/blogs/{id}', [SiteController::class, 'blogRedirectFromId'])->whereNumber('id');
Route::get('/blogs/{blog:slug}', [SiteController::class, 'blogShow'])->where('blog', '.*')->name('blogs.show');

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

Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/editorial', [StaticPageController::class, 'editorial'])->name('editorial');
Route::get('/privacy', [StaticPageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [StaticPageController::class, 'terms'])->name('terms');
Route::get('/contact', [StaticPageController::class, 'contact'])->name('contact');
Route::get('/advertise', [StaticPageController::class, 'advertise'])->name('advertise');
Route::get('/ads.txt', [StaticPageController::class, 'adsTxt'])->name('ads.txt');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// ── Admin Dashboard ─────────────────────────────────────────
// Mounted by Filament's AdminPanelProvider at /dashboard.
