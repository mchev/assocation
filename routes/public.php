<?php

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
