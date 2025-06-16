<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function show(Request $request)
    {
        $cart = session()->get('cart', []);
        $items = [];
        $groupedItems = [];

        foreach ($cart as $item) {
            $equipment = Equipment::with('organization')->find($item['equipment_id']);
            if ($equipment) {
                $days = abs(Carbon::parse($item['rental_end'])->diffInDays(Carbon::parse($item['rental_start']))) + 1;
                $itemData = [
                    'equipment' => $equipment,
                    'image' => $equipment->photos ? $equipment->photos[0]->url : null,
                    'rental_start' => $item['rental_start'],
                    'rental_end' => $item['rental_end'],
                    'quantity' => $item['quantity'],
                    'price' => $equipment->rental_price,
                    'days' => $days,
                    'deposit' => $equipment->requires_deposit ? $equipment->deposit_amount * $item['quantity'] : 0,
                    'total' => $equipment->rental_price * $days * $item['quantity'],
                ];

                $orgId = $equipment->organization->id;
                if (! isset($groupedItems[$orgId])) {
                    $groupedItems[$orgId] = [
                        'organization' => $equipment->organization,
                        'items' => [],
                        'total' => 0,
                        'deposit' => 0,
                    ];
                }
                $groupedItems[$orgId]['items'][] = $itemData;
                $groupedItems[$orgId]['total'] += $itemData['total'];
                $groupedItems[$orgId]['deposit'] += $itemData['deposit'];
            }
        }

        return Inertia::render('Public/Carts/Show', [
            'items' => array_values($groupedItems),
        ]);
    }

    public function add(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([

            'rental_start' => 'required|date',
            'rental_end' => 'required|date|after:rental_start',
            'quantity' => 'required|integer|min:1',
        ]);

        $startDate = Carbon::parse($validated['rental_start'])->format('Y-m-d');
        $endDate = Carbon::parse($validated['rental_end'])->format('Y-m-d');

        $cart = session()->get('cart', []);
        $key = $equipment->id.'-'.$startDate.'-'.$endDate;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $validated['quantity'];
        } else {
            $cart[$key] = [
                'equipment_id' => $equipment->id,
                'rental_start' => $startDate,
                'rental_end' => $endDate,
                'quantity' => $validated['quantity'],
            ];
        }

        session()->put('cart', $cart);

        return back();
    }

    public function remove(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'rental_start' => 'required|date',
            'rental_end' => 'required|date|after:rental_start',
        ]);

        $key = $equipment->id.'-'.$validated['rental_start'].'-'.$validated['rental_end'];

        $cart = session()->get('cart');
        unset($cart[$key]);
        session()->put('cart', $cart);

        return back();
    }

    public function update(Request $request, $key)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $validated['quantity'];
            session()->put('cart', $cart);
        }

        return back();
    }

    public function clear()
    {
        session()->forget('cart');

        return back();
    }
}
