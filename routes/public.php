<?php

use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Public\EquipmentController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
Route::get('/equipments/{equipment}', [EquipmentController::class, 'show'])->name('equipments.show');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
});
