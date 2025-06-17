<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\OrganizationInvitationNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationMemberController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->currentOrganization;

        return Inertia::render('App/Organizations/Settings/Members/Index', [
            'organization' => $organization,
            'members' => $organization->users()->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->pivot->role,
                    'status' => 'active',
                ];
            }),
            'pendingInvitations' => $organization->invitations()
                ->whereNull('accepted_at')
                ->get()
                ->map(function ($invitation) {
                    return [
                        'id' => $invitation->id,
                        'email' => $invitation->email,
                        'role' => $invitation->role,
                        'created_at' => $invitation->created_at,
                    ];
                }),
            'roles' => [
                ['id' => 'admin', 'name' => 'Administrateur'],
                ['id' => 'member', 'name' => 'Membre'],
            ],
            'section' => 'members',
        ]);
    }

    public function invite(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:admin,member'],
        ]);

        $organization = $request->user()->currentOrganization;
        $user = User::where('email', $validated['email'])->first();

        $invitation = $organization->invitations()->create([
            'email' => $validated['email'],
            'role' => $validated['role'],
            'token' => \Str::random(32),
        ]);

        if ($user) {
            $user->notify(new OrganizationInvitationNotification($invitation));
        } else {
            \Notification::route('mail', $validated['email'])
                ->notify(new OrganizationInvitationNotification($invitation));
        }

        return redirect()->back()->with('success', 'Invitation envoyée avec succès');
    }

    public function remove(Request $request, User $member)
    {
        $organization = $request->user()->currentOrganization;

        if ($member->id === $organization->owner_id) {
            return back()->with('error', 'Cannot remove organization owner');
        }

        $organization->users()->detach($member->id);

        return back()->with('success', 'Membre retiré avec succès');
    }

    public function updateRole(Request $request, User $member)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,member'],
        ]);

        $organization = $request->user()->currentOrganization;

        if ($member->id === $organization->owner_id) {
            return back()->with('error', 'Cannot change organization owner role');
        }

        $organization->users()->updateExistingPivot($member->id, [
            'role' => $validated['role'],
        ]);

        return back()->with('status', 'role-updated');
    }

    public function cancelInvitation(Request $request, $invitation)
    {
        $organization = $request->user()->currentOrganization;

        $invitation = $organization->invitations()
            ->whereNull('accepted_at')
            ->findOrFail($invitation);

        $invitation->delete();

        return back()->with('success', 'Invitation annulée avec succès');
    }
}
