<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'API is running',
        'version' => config('app.version'),
        'status' => 'operational',
        'timestamp' => now()->toIso8601String(),
        'environment' => config('app.env'),
    ]);
})->name('api.status');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware'),
])->group(function () {});

// Equipment routes
Route::get('/equipments/{equipment}/available-quantity', [EquipmentController::class, 'availableQuantity'])
    ->name('api.equipments.available-quantity');
Route::get('/equipments/{equipment}/reservations-dates-by-month', [EquipmentController::class, 'reservationsDatesByMonth'])
    ->name('api.equipments.reservations-dates-by-month');

// Category routes
Route::apiResource('categories', CategoryController::class);
Route::get('categories/tree', [CategoryController::class, 'tree']);

Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('api.search.suggestions');

Route::get('/organizations', [OrganizationController::class, 'index'])->name('api.organizations.index');
