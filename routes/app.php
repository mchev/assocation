<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\OrganizationController;
use App\Http\Controllers\App\EquipmentController;
use App\Http\Controllers\App\DepotController;
use App\Http\Controllers\App\ReservationController;
use App\Http\Controllers\App\CalendarController;
use App\Http\Controllers\App\ProfileController;

Route::middleware(['auth', 'verified'])->prefix('app')->name('app.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('organizations', OrganizationController::class);
    Route::resource('organizations.equipments', EquipmentController::class);
    Route::resource('equipments', EquipmentController::class);
    Route::resource('depots', DepotController::class);
    Route::resource('reservations', ReservationController::class);
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
}); 