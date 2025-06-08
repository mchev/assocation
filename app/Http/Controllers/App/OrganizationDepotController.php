<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationDepotController extends Controller
{
    /**
     * Display the depots management page.
     */
    public function index(Request $request)
    {
        return Inertia::render('App/Organizations/Settings/Show', [
            'organization' => $request->user()->currentOrganization,
            'section' => 'depots',
            'depots' => $request->user()->currentOrganization->depots()->latest()->get(),
        ]);
    }

    /**
     * Store a newly created depot in storage.
     */
    public function store(Request $request)
    {
        $organization = $request->user()->currentOrganization;
        $this->authorize('create', $organization);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
        ]);

        $organization->depots()->create($validated);

        return back()->with('status', 'depot-created');
    }

    /**
     * Update the specified depot in storage.
     */
    public function update(Request $request, Depot $depot)
    {
        $this->authorize('update', $depot);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
        ]);

        $depot->update($validated);

        return back()->with('status', 'depot-updated');
    }

    /**
     * Remove the specified depot from storage.
     */
    public function destroy(Depot $depot)
    {
        $this->authorize('delete', $depot);

        $depot->delete();

        return back()->with('status', 'depot-deleted');
    }
}
