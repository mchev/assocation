<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function index()
    {
        return Inertia::render('Public/Checkout/Index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.equipment_id' => 'required|exists:equipments,id',
            'items.*.start_date' => 'required|date',
            'items.*.end_date' => 'required|date|after:items.*.start_date',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:255',
        ]);

        try {
            $reservations = $this->reservationService->createMultipleReservations(
                $validated['items'],
                $validated['contact_name'],
                $validated['contact_email'],
                $validated['contact_phone']
            );

            return redirect()
                ->route('checkout.success')
                ->with('success', 'Your reservations have been created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function success()
    {
        return Inertia::render('Public/Checkout/Success');
    }
} 