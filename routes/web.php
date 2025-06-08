<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('carts')->name('carts.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/show', [CartController::class, 'show'])->name('show');
    Route::post('/add/{equipment}', [CartController::class, 'add'])->name('add');
    Route::delete('/remove/{key}', [CartController::class, 'remove'])->name('remove');
    Route::put('/update/{key}', [CartController::class, 'update'])->name('update');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/decouvrir', 'discover')->name('discover');
    Route::get('/comment-ca-marche', 'howItWorks')->name('how-it-works');
});
