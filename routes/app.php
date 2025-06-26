<?php

use App\Http\Controllers\App\CalendarController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\EquipmentController;
use App\Http\Controllers\App\OrganizationController;
use App\Http\Controllers\App\OrganizationDepotController;
use App\Http\Controllers\App\OrganizationMemberController;
use App\Http\Controllers\App\OrganizationSettingsController;
use App\Http\Controllers\App\ReservationInController;
use App\Http\Controllers\App\ReservationOutController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('app')->name('app.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::post('organizations/switch/{organization}', [OrganizationController::class, 'switch'])->name('organizations.switch');

    Route::get('organizations/settings', [OrganizationSettingsController::class, 'edit'])->name('organizations.settings.edit');
    Route::patch('organizations/settings', [OrganizationSettingsController::class, 'update'])->name('organizations.settings.update');

    // Members Management
    Route::get('organizations/members', [OrganizationMemberController::class, 'index'])->name('organizations.members.index');
    Route::post('organizations/members/invite', [OrganizationMemberController::class, 'invite'])->name('organizations.members.invite');
    Route::delete('organizations/members/{member}', [OrganizationMemberController::class, 'remove'])->name('organizations.members.remove');
    Route::patch('organizations/members/{member}/role', [OrganizationMemberController::class, 'updateRole'])->name('organizations.members.update-role');
    Route::delete('organizations/invitations/{invitation}', [OrganizationMemberController::class, 'cancelInvitation'])->name('organizations.invitations.cancel');

    // Depots Management
    Route::get('organizations/depots', [OrganizationDepotController::class, 'index'])->name('organizations.depots.index');
    Route::post('organizations/depots', [OrganizationDepotController::class, 'store'])->name('organizations.depots.store');
    Route::get('organizations/depots/create', [OrganizationDepotController::class, 'create'])->name('organizations.depots.create');
    Route::get('organizations/depots/{depot}/edit', [OrganizationDepotController::class, 'edit'])->name('organizations.depots.edit');
    Route::put('organizations/depots/{depot}', [OrganizationDepotController::class, 'update'])->name('organizations.depots.update');
    Route::delete('organizations/depots/{depot}', [OrganizationDepotController::class, 'destroy'])->name('organizations.depots.destroy');

    // Delete Organization
    Route::get('organizations/settings/delete', [OrganizationSettingsController::class, 'delete'])->name('organizations.settings.delete');

    Route::resource('organizations', OrganizationController::class)->only(['create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('/calendar', [CalendarController::class, 'index'])->name('organizations.calendar');

    // Equipments Management
    Route::prefix('equipments')->name('organizations.equipments.')->group(function () {
        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::get('/create', [EquipmentController::class, 'create'])->name('create');
        Route::post('/', [EquipmentController::class, 'store'])->name('store');
        Route::get('/{equipment}/edit', [EquipmentController::class, 'edit'])->name('edit');
        Route::put('/{equipment}', [EquipmentController::class, 'update'])->name('update');
        Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');
    });

    // (in) Reservations Management
    Route::prefix('reservations/in')->name('organizations.reservations.in.')->group(function () {
        Route::get('/', [ReservationInController::class, 'index'])->name('index');
        Route::post('/', [ReservationInController::class, 'store'])->name('store');
        Route::get('/{reservation}/edit', [ReservationInController::class, 'edit'])->name('edit');
        Route::delete('/{reservation}', [ReservationInController::class, 'destroy'])->name('destroy');
    });

    // (out) Reservations Management
    Route::prefix('reservations/out')->name('organizations.reservations.out.')->group(function () {
        Route::get('/', [ReservationOutController::class, 'index'])->name('index');
        Route::get('/create', [ReservationOutController::class, 'create'])->name('create');
        Route::post('/', [ReservationOutController::class, 'store'])->name('store');
        Route::get('/{reservation}', [ReservationOutController::class, 'show'])->name('show');
        Route::get('/{reservation}/edit', [ReservationOutController::class, 'edit'])->name('edit');
        Route::put('/{reservation}', [ReservationOutController::class, 'update'])->name('update');
        Route::delete('/{reservation}', [ReservationOutController::class, 'destroy'])->name('destroy');

        // Reservation Status Management
        Route::put('/{reservation}/confirm', [ReservationOutController::class, 'confirm'])->name('confirm');
        Route::put('/{reservation}/reject', [ReservationOutController::class, 'reject'])->name('reject');
        Route::put('/{reservation}/cancel', [ReservationOutController::class, 'cancel'])->name('cancel');
        Route::put('/{reservation}/complete', [ReservationOutController::class, 'complete'])->name('complete');

        // Reservation Items Management
        Route::post('/{reservation}/items/{item}/pickup', [ReservationOutController::class, 'pickupItem'])->name('items.pickup');
        Route::post('/{reservation}/items/{item}/return', [ReservationOutController::class, 'returnItem'])->name('items.return');
        Route::post('/{reservation}/discount', [ReservationOutController::class, 'applyDiscount'])->name('discount.apply');
        Route::delete('/{reservation}/discount', [ReservationOutController::class, 'removeDiscount'])->name('discount.remove');
    });

});
