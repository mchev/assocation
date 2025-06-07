<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepotController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $organization = $user->currentOrganization;
        $depots = $organization->depots;

        return Inertia::render('App/Organizations/Depots/Index', [
            'depots' => $depots,
        ]);
    }

    public function create()
    {
        return Inertia::render('App/Organizations/Depots/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $organization = $request->user()->currentOrganization;
        $organization->depots()->create($request->all());

        return redirect()->route('app.depots.index')->with('success', 'Depot created successfully');
    }

    public function edit(Depot $depot)
    {
        return Inertia::render('App/Organizations/Depots/Edit', [
            'depot' => $depot,
        ]);
    }

    public function update(Request $request, Depot $depot)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $depot->update($request->all());

        return redirect()->route('app.depots.index')->with('success', 'Depot updated successfully');
    }

    public function destroy(Depot $depot)
    {
        $depot->delete();

        return redirect()->route('app.depots.index')->with('success', 'Depot deleted successfully');
    }
}
