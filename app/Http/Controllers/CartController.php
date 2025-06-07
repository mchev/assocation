<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        return Inertia::render('Public/Carts/Show');
    }

    public function show()
    {
        return Inertia::render('Public/Carts/Show');
    }

    public function add(Request $request, Equipment $equipment)
    {
        $cart = session('cart', []);
        $key = uniqid();

        $cart[$key] = [
            'equipment_id' => $equipment->id,
            'rental_start' => $request->rental_start,
            'rental_end' => $request->rental_end,
            'quantity' => $request->quantity ?? 1,
            'notes' => $request->notes,
        ];

        session(['cart' => $cart]);

        return back()->with('success', 'Article ajouté au camion');
    }

    public function remove($key)
    {
        $cart = session('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session(['cart' => $cart]);
        }

        return back();
    }

    public function update(Request $request, $key)
    {
        $cart = session('cart', []);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $request->quantity;
            session(['cart' => $cart]);
        }

        return back();
    }

    public function clear()
    {
        session()->forget('cart');

        return back();
    }
}
