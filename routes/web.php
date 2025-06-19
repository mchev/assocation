<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\HelloassoController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\EquipmentController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/equipments/{equipment}', [EquipmentController::class, 'show'])->name('equipments.show');

// Member invitations
Route::middleware('auth')->group(function () {
    Route::get('/invitations/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
});

Route::prefix('carts')->name('carts.')->group(function () {
    Route::get('/', [CartController::class, 'show'])->name('show');
    Route::post('/add/{equipment}', [CartController::class, 'add'])->name('add');
    Route::delete('/remove/{equipment}', [CartController::class, 'remove'])->name('remove');
    Route::put('/update/{equipment}', [CartController::class, 'update'])->name('update');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::controller(PageController::class)->group(function () {
    Route::get('/decouvrir', 'discover')->name('discover');
    Route::get('/comment-ca-marche', 'howItWorks')->name('how-it-works');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/mentions-legales', 'terms')->name('terms');
    Route::get('/politique-de-confidentialite', 'privacy')->name('privacy');
    Route::get('/conditions-generales-d-utilisation', 'conditions')->name('conditions');
    Route::get('/sitemap.xml', 'sitemap')->name('sitemap');
    Route::get('/sitemap-index.xml', 'sitemapIndex')->name('sitemap.index');
    Route::get('/sitemap-static.xml', 'sitemapStatic')->name('sitemap.static');
    Route::get('/sitemap-{type}-{page}.xml', 'sitemapDynamic')->name('sitemap.dynamic');
});

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::get('/auth/helloasso/redirect', [HelloassoController::class, 'redirect'])->name('helloasso.redirect');
Route::get('/auth/helloasso/callback', [HelloassoController::class, 'callback'])->name('helloasso.callback');
