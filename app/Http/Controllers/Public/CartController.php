<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
        $items = [];

        foreach ($cart as $item) {
            $equipment = Equipment::find($item['equipment_id']);
            if ($equipment) {
                $items[] = [
                    'equipment' => $equipment,
                    'start_date' => $item['start_date'],
                    'end_date' => $item['end_date'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ];
            }
        }

        return Inertia::render('Public/Cart/Index', [
            'items' => $items,
        ]);
    }

    public function add(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);
        $key = $equipment->id.'-'.$validated['start_date'].'-'.$validated['end_date'];

        if (isset($cart[$key])) {
            $cart[$key]['quantity']++;
        } else {
            $cart[$key] = [
                'equipment_id' => $equipment->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'quantity' => 1,
                'notes' => $validated['notes'] ?? null,
            ];
        }

        session()->put('cart', $cart);

        return back();
    }

    public function remove(Request $request, $key)
    {
        $cart = session()->get('cart', []);
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
