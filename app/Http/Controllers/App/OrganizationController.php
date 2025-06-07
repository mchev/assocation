<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Http\Requests\Organization\StoreRequest;
use App\Http\Requests\Organization\UpdateRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrganizationController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Organization::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organizations = auth()->user()->organizations()
            ->withCount(['equipments', 'users'])
            ->get();

        return Inertia::render('App/Organizations/Index', [
            'organizations' => $organizations,
            'can' => [
                'create' => auth()->user()->can('create', Organization::class),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('App/Organizations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $organization = Organization::create($request->validated());

        // Attach user as admin
        $organization->users()->attach(auth()->id(), ['role' => 'admin']);

        return redirect()
            ->route('app.organizations.show', $organization)
            ->with('success', 'Organization created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        $organization->load(['equipments', 'users']);

        return Inertia::render('App/Organizations/Show', [
            'organization' => $organization,
            'can' => [
                'update' => auth()->user()->can('update', $organization),
                'delete' => auth()->user()->can('delete', $organization),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization)
    {
        return Inertia::render('App/Organizations/Edit', [
            'organization' => $organization,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Organization $organization)
    {
        $organization->update($request->validated());

        return redirect()
            ->route('app.organizations.show', $organization)
            ->with('success', 'Organization updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()
            ->route('app.organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }

    /**
     * Switch to a different organization.
     */
    public function switch(Organization $organization)
    {
        $user = auth()->user();

        // Check if user is a member of the organization
        if (!$user->organizations->contains($organization)) {
            return back()->with('error', 'Vous n\'êtes pas membre de cette organisation.');
        }

        // Use the trait's method to set the primary organization
        $user->setPrimaryOrganization($organization);

        return back()->with('success', 'Organisation principale mise à jour avec succès.');
    }
} 