<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::prefix('carts')->name('carts.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/show', [CartController::class, 'show'])->name('show');
    Route::post('/add/{equipment}', [CartController::class, 'add'])->name('add');
    Route::delete('/remove/{key}', [CartController::class, 'remove'])->name('remove');
    Route::put('/update/{key}', [CartController::class, 'update'])->name('update');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/decouvrir', 'discover')->name('discover');
    Route::get('/comment-ca-marche', 'howItWorks')->name('how-it-works');
});

Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function () {
    $user = Socialite::driver('google')->user();
    dd($user);
});
