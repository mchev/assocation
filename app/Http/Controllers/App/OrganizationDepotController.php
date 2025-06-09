<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Depots\StoreRequest;
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
        $organization = $request->user()->currentOrganization;

        return Inertia::render('App/Organizations/Settings/Depots/Index', [
            'organization' => $organization,
            'depots' => $organization->depots()->with('equipments:id,name,depot_id')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('App/Organizations/Settings/Depots/Create');
    }

    /**
     * Store a newly created depot in storage.
     */
    public function store(StoreRequest $request)
    {
        $organization = $request->user()->currentOrganization;
        $this->authorize('create', $organization);

        $organization->depots()->create($request->validated());

        return redirect()->route('app.organizations.depots.index')->withSuccess('Le dépôt a été créé avec succès');
    }

    public function edit(Depot $depot)
    {
        return Inertia::render('App/Organizations/Settings/Depots/Edit', [
            'depot' => $depot,
        ]);
    }

    /**
     * Update the specified depot in storage.
     */
    public function update(StoreRequest $request, Depot $depot)
    {
        $this->authorize('update', $depot);

        $depot->update($request->validated());

        return redirect()->route('app.organizations.depots.index')->withSuccess('Le dépôt a été mis à jour avec succès');
    }

    /**
     * Remove the specified depot from storage.
     */
    public function destroy(Depot $depot)
    {
        $this->authorize('delete', $depot);

        // Load equipments count if not already loaded
        if (! $depot->relationLoaded('equipments')) {
            $depot->load('equipments:id,name,depot_id');
        }

        $depot->delete();

        return redirect()->route('app.organizations.depots.index')->withSuccess('Le dépôt a été supprimé avec succès');
    }
}
