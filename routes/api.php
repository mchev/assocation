<?php

use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\CategoryController;
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

// Category routes
Route::apiResource('categories', CategoryController::class);
Route::get('categories/tree', [CategoryController::class, 'tree']);

// Routes pour les disponibilités
Route::get('/equipments/{equipment}/availability', [AvailabilityController::class, 'getMonthlyAvailability']);
Route::post('/equipments/{equipment}/availability', [AvailabilityController::class, 'updateAvailability']);

Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('api.search.suggestions');
