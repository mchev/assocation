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

    Route::get('organizations/settings', [OrganizationSettingsController::class, 'show'])->name('organizations.settings');
    Route::patch('organizations/settings', [OrganizationSettingsController::class, 'update'])->name('organizations.settings.update');

    // Members Management
    Route::get('organizations/settings/members', [OrganizationMemberController::class, 'index'])->name('organizations.settings.members');
    Route::post('organizations/members/invite', [OrganizationMemberController::class, 'invite'])->name('organizations.members.invite');
    Route::delete('organizations/members/{member}', [OrganizationMemberController::class, 'remove'])->name('organizations.members.remove');
    Route::patch('organizations/members/{member}/role', [OrganizationMemberController::class, 'updateRole'])->name('organizations.members.update-role');
    Route::delete('organizations/invitations/{invitation}', [OrganizationMemberController::class, 'cancelInvitation'])->name('organizations.invitations.cancel');

    // Depots Management
    Route::get('organizations/depots', [OrganizationDepotController::class, 'index'])->name('organizations.depots.index');
    Route::post('organizations/depots', [OrganizationDepotController::class, 'store'])->name('organizations.depots.store');
    Route::get('organizations/depots/{depot}', [OrganizationDepotController::class, 'show'])->name('organizations.depots.show');
    Route::put('organizations/depots/{depot}', [OrganizationDepotController::class, 'update'])->name('organizations.depots.update');
    Route::delete('organizations/depots/{depot}', [OrganizationDepotController::class, 'destroy'])->name('organizations.depots.destroy');

    // Delete Organization
    Route::get('organizations/settings/delete', [OrganizationSettingsController::class, 'delete'])->name('organizations.settings.delete');
    Route::delete('organizations', [OrganizationController::class, 'destroy'])->name('organizations.destroy');

    Route::resource('organizations', OrganizationController::class);
    Route::resource('organizations.equipments', EquipmentController::class);
    Route::resource('organizations.reservations', ReservationController::class);
    Route::get('/organizations.calendar', [CalendarController::class, 'index'])->name('organizations.calendar');
});
