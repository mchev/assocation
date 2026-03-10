<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EquipmentController as AdminEquipmentController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class)->names('users');
    Route::resource('categories', AdminCategoryController::class)->names('categories');

    Route::get('equipments', [AdminEquipmentController::class, 'index'])->name('equipments.index');
    Route::get('equipments/{equipment}', [AdminEquipmentController::class, 'show'])->name('equipments.show');
    Route::post('equipments/{equipment}/images', [AdminEquipmentController::class, 'storeImage'])->name('equipments.images.store');
    Route::post('equipments/{equipment}/generate-image', [AdminEquipmentController::class, 'generateImage'])->name('equipments.generate-image');
    Route::delete('equipments/{equipment}/images/{image}', [AdminEquipmentController::class, 'destroyImage'])->name('equipments.images.destroy');

    Route::get('newsletters/{newsletter}/preview', [AdminNewsletterController::class, 'preview'])->name('newsletters.preview');
    Route::post('newsletters/{newsletter}/send-test', [AdminNewsletterController::class, 'sendTest'])->name('newsletters.send-test');
    Route::post('newsletters/{newsletter}/send', [AdminNewsletterController::class, 'send'])->name('newsletters.send');
    Route::post('newsletters/{newsletter}/send-to-all-users', [AdminNewsletterController::class, 'sendToAllUsers'])->name('newsletters.send-to-all-users');
    Route::get('newsletters/{newsletter}/stats', [AdminNewsletterController::class, 'stats'])->name('newsletters.stats');
    Route::resource('newsletters', AdminNewsletterController::class)->names('newsletters')->except(['show']);
});
