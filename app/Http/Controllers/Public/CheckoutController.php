<?php

namespace App\Http\Controllers\Public;

use App\Actions\Reservations\CreateCartReservation;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index()
    {
        return Inertia::render('Public/Checkout/Index');
    }

    public function store(Request $request, CreateCartReservation $action)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.equipment_id' => 'required|exists:equipments,id',
            'items.*.start_date' => 'required|date',
            'items.*.end_date' => 'required|date|after:items.*.start_date',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
            'to_organization_id' => 'required|exists:organizations,id',
        ]);

        try {
            $fromOrganization = $request->user()->currentOrganization;
            $toOrganization = Organization::findOrFail($validated['to_organization_id']);

            $reservation = $action->handle(
                $validated['items'],
                $fromOrganization,
                $toOrganization
            );

            // Vider le panier après création réussie
            session()->forget('cart');

            return redirect()
                ->route('checkout.success')
                ->with('success', 'Votre réservation a été créée avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function success()
    {
        return Inertia::render('Public/Checkout/Success');
    }
}
