<?php

use App\Http\Controllers\App\CalendarController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\EquipmentController;
use App\Http\Controllers\App\OrganizationController;
use App\Http\Controllers\App\OrganizationDepotController;
use App\Http\Controllers\App\OrganizationMemberController;
use App\Http\Controllers\App\OrganizationSettingsController;
use App\Http\Controllers\App\ReservationController;
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
        Route::get('/{equipment}', [EquipmentController::class, 'show'])->name('show');
        Route::get('/{equipment}/edit', [EquipmentController::class, 'edit'])->name('edit');
        Route::put('/{equipment}', [EquipmentController::class, 'update'])->name('update');
        Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');
    });

    // Reservations Management
    Route::prefix('reservations')->name('organizations.reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::get('/create', [ReservationController::class, 'create'])->name('create');
        Route::post('/', [ReservationController::class, 'store'])->name('store');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        Route::get('/{reservation}/edit', [ReservationController::class, 'edit'])->name('edit');
        Route::put('/{reservation}', [ReservationController::class, 'update'])->name('update');
        Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('destroy');

        // Reservation Status Management
        Route::post('/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('confirm');
        Route::post('/{reservation}/reject', [ReservationController::class, 'reject'])->name('reject');
        Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
        Route::post('/{reservation}/complete', [ReservationController::class, 'complete'])->name('complete');

        // Reservation Items Management
        Route::post('/{reservation}/items/{item}/pickup', [ReservationController::class, 'pickupItem'])->name('items.pickup');
        Route::post('/{reservation}/items/{item}/return', [ReservationController::class, 'returnItem'])->name('items.return');
        Route::post('/{reservation}/discount', [ReservationController::class, 'applyDiscount'])->name('discount.apply');
        Route::delete('/{reservation}/discount', [ReservationController::class, 'removeDiscount'])->name('discount.remove');
    });
});
