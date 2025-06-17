<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\EquipmentController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
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
    Route::get('/mentions-legales', 'terms')->name('terms');
    Route::get('/politique-de-confidentialite', 'privacy')->name('privacy');
    Route::get('/conditions-generales-d-utilisation', 'conditions')->name('conditions');
});

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
