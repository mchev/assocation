<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationSettingsController extends Controller
{
    public function show(Request $request)
    {
        return Inertia::render('App/Organizations/Settings/Show', [
            'organization' => $request->user()->currentOrganization,
            'section' => 'general',
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        $request->user()->currentOrganization->update($validated);

        return back()->with('status', 'organization-updated');
    }

    public function delete(Request $request)
    {
        return Inertia::render('App/Organizations/Settings/Show', [
            'organization' => $request->user()->currentOrganization,
            'section' => 'delete',
        ]);
    }
}
