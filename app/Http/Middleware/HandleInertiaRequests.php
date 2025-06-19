<?php

namespace App\Http\Middleware;

use App\Models\Equipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

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
                $days = abs(Carbon::parse($item['rental_start'])->diffInDays(Carbon::parse($item['rental_end'])) + 1);
                $cartItems[] = [
                    'key' => $key,
                    'equipment' => [
                        'id' => $equipment->id,
                        'name' => $equipment->name,
                        'image' => $equipment->image,
                        'rental_price' => $equipment->rental_price,
                        'requires_deposit' => $equipment->requires_deposit,
                        'deposit_amount' => $equipment->deposit_amount,
                    ],
                    'days' => $days,
                    'rental_start' => $item['rental_start'],
                    'rental_end' => $item['rental_end'],
                    'quantity' => $item['quantity'],
                    'total_price' => $equipment->rental_price * $days * $item['quantity'],
                    'total_deposit' => $equipment->deposit_amount * $days * $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ];
            }
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'app_url' => config('app.url'),
            'canonical' => $request->url(),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'city' => $user->city,
                    'postal_code' => $user->postal_code,
                    'country' => $user->country,
                    'preferred_language' => $user->preferred_language,
                    'organizations' => $user->organizations->map(function ($organization) {
                        return [
                            'id' => $organization->id,
                            'name' => $organization->name,
                            'role' => $organization->pivot->role,
                            'is_primary' => $organization->pivot->is_primary,
                        ];
                    }),
                    'current_organization' => $user->currentOrganization()->select('id', 'name')->first(),
                    'can' => $user ? [
                        'equipments' => [
                            'create' => $user->can('create', [Equipment::class, $user->currentOrganization]),
                            'update' => $user->can('update', new Equipment(['organization_id' => $user->currentOrganization->id])),
                            'delete' => $user->can('delete', new Equipment(['organization_id' => $user->currentOrganization->id])),
                        ],
                        // Add other resource permissions here as needed
                    ] : [],
                ] : null,
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'cart' => $cartItems,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'message' => $request->session()->get('message'),
            ],
        ];
    }
}
