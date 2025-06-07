<?php

use App\Http\Controllers\App\CalendarController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\DepotController;
use App\Http\Controllers\App\EquipmentController;
use App\Http\Controllers\App\OrganizationController;
use App\Http\Controllers\App\ReservationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('app')->name('app.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('organizations', OrganizationController::class);
    Route::resource('organizations.equipments', EquipmentController::class);
    Route::resource('organizations.depots', DepotController::class);
    Route::resource('organizations.reservations', ReservationController::class);
    Route::get('/organizations.calendar', [CalendarController::class, 'index'])->name('organizations.calendar');
});
