<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationPreferencesService
{
    private const GUEST_CACHE_TTL = 86400; // 24 hours

    private const USER_CACHE_TTL = 2592000; // 30 days

    private const REQUIRED_LOCATION_FIELDS = ['coordinates', 'radius', 'city', 'postcode'];

    public function getLocationPreferences(Request $request): ?array
    {
        if ($request->user()) {
            return $this->getAuthenticatedUserPreferences($request->user());
        }

        return $this->getGuestPreferences($request);
    }

    public function updateLocationPreferences(Request $request): ?array
    {
        $locationData = $request->only(self::REQUIRED_LOCATION_FIELDS);

        if (! $this->hasValidLocationData($locationData)) {
            return null;
        }

        if ($request->user()) {
            return $this->updateAuthenticatedUserPreferences($request->user(), $locationData);
        }

        return $this->updateGuestPreferences($locationData);
    }

    private function getAuthenticatedUserPreferences(User $user): ?array
    {
        $cacheKey = $this->getUserCacheKey($user);

        return Cache::remember($cacheKey, self::USER_CACHE_TTL, function () use ($user) {
            return $user->preferences;
        });
    }

    private function getGuestPreferences(Request $request): ?array
    {
        $cacheKey = $this->getGuestCacheKey($request);

        return Cache::get($cacheKey);
    }

    private function updateAuthenticatedUserPreferences(User $user, array $locationData): array
    {
        $user->update(['preferences' => $locationData]);

        $cacheKey = $this->getUserCacheKey($user);
        Cache::put($cacheKey, $locationData, self::USER_CACHE_TTL);

        return $locationData;
    }

    private function updateGuestPreferences(array $locationData): array
    {
        $cacheKey = $this->getGuestCacheKey(request());
        Cache::put($cacheKey, $locationData, self::GUEST_CACHE_TTL);

        return $locationData;
    }

    private function hasValidLocationData(?array $locationData): bool
    {
        if (! $locationData) {
            return false;
        }
        // check if latitude and longitude exists and are not null
        if (! isset($locationData['coordinates']['lat']) || ! isset($locationData['coordinates']['lng'])) {
            return false;
        }

        return collect(self::REQUIRED_LOCATION_FIELDS)
            ->every(fn ($field) => ! empty($locationData[$field]));
    }

    private function getUserCacheKey(User $user): string
    {
        return "user_location_preferences_{$user->id}";
    }

    private function getGuestCacheKey(Request $request): string
    {
        return 'guest_location_preferences_'.md5($request->ip());
    }
}
