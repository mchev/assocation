<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->current_organization) {
            return redirect()->route('organizations.create')
                ->with('error', 'Vous devez créer ou rejoindre une organisation.');
        }

        return $next($request);
    }
}
