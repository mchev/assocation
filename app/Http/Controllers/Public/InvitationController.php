<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\OrganizationInvitation;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function accept(Request $request, $token)
    {
        $invitation = OrganizationInvitation::where('token', $token)->first();
        $user = $request->user();

        if (! $invitation) {
            return redirect()->route('home')->with('error', 'Invitation invalide');
        }

        if ($invitation->email !== $user->email) {
            return redirect()->route('home')->with('error', 'Vous ne pouvez pas accepter cette invitation');
        }

        $user->organizations()->attach($invitation->organization_id, ['role' => $invitation->role]);
        $invitation->update(['accepted_at' => now()]);

        return redirect()->route('home')->with('success', 'Vous avez rejoint l\'organisation');

    }
}
