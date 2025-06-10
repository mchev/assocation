<?php

namespace Tests\Unit\Services;

use App\Services\LocationPreferencesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

test('it stores location preferences for authenticated user', function () {
    $user = createUser();

    $locationData = [
        'coordinates' => ['lat' => 48.8566, 'lng' => 2.3522],
        'radius' => 10,
        'city' => 'Paris',
        'postcode' => '75001',
    ];

    $request = Request::create('/', 'GET', $locationData);
    $request->setUserResolver(fn () => $user);

    $service = new LocationPreferencesService;
    $preferences = $service->updateLocationPreferences($request);

    // Check database update
    expect($user->fresh()->preferences)->toBe($locationData);

    // Check cache
    $cacheKey = "user_location_preferences_{$user->id}";
    expect(Cache::get($cacheKey))->toBe($locationData);
});

test('it stores location preferences for guest user', function () {
    $locationData = [
        'coordinates' => ['lat' => 48.8566, 'lng' => 2.3522],
        'radius' => 10,
        'city' => 'Paris',
        'postcode' => '75001',
    ];

    $request = Request::create('/', 'GET', $locationData);
    $request->server->set('REMOTE_ADDR', '127.0.0.1');

    $service = new LocationPreferencesService;
    $preferences = $service->updateLocationPreferences($request);

    // Check cache
    $cacheKey = 'guest_location_preferences_'.md5('127.0.0.1');
    expect(Cache::get($cacheKey))->toBe($locationData);
});

test('it returns null for invalid location data', function () {
    $request = Request::create('/', 'GET', [
        'coordinates' => ['lat' => 48.8566], // Missing lng
        'radius' => 10,
        'city' => 'Paris',
        'postcode' => '75001',
    ]);

    $service = new LocationPreferencesService;
    $preferences = $service->updateLocationPreferences($request);

    expect($preferences)->toBeNull();
});

test('it retrieves cached location preferences', function () {
    $user = createUser();

    $locationData = [
        'coordinates' => ['lat' => 48.8566, 'lng' => 2.3522],
        'radius' => 10,
        'city' => 'Paris',
        'postcode' => '75001',
    ];

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);

    Cache::put("user_location_preferences_{$user->id}", $locationData, now()->addDay());

    $service = new LocationPreferencesService;
    $preferences = $service->getLocationPreferences($request);

    expect($preferences)->toBe($locationData);
});
