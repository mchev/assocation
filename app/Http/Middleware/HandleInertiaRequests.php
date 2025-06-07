<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Equipment;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $cart = session('cart', []);
        $cartItems = [];

        foreach ($cart as $key => $item) {
            $equipment = Equipment::with(['category', 'depot'])
                ->find($item['equipment_id']);

            if ($equipment) {
                $cartItems[] = [
                    'key' => $key,
                    'equipment' => [
                        'id' => $equipment->id,
                        'name' => $equipment->name,
                        'image' => $equipment->image,
                        'price' => $equipment->price,
                        'deposit' => $equipment->deposit,
                    ],
                    'rental_start' => $item['rental_start'],
                    'rental_end' => $item['rental_end'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ];
            }
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'organizations' => $user->organizations->map(function ($organization) {
                        return [
                            'id' => $organization->id,
                            'name' => $organization->name,
                            'role' => $organization->pivot->role,
                            'is_primary' => $organization->pivot->is_primary,
                        ];
                    }),
                    'primary_organization' => $user->primaryOrganization(),
                ] : null,
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'cart' => $cartItems,
        ];
    }
}
