<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdminMfa
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ((string) ($user->role ?? '') !== 'SUPERADMIN') {
            return $next($request);
        }

        $enforceMfa = (bool) config('admin-security.require_superadmin_mfa', true);
        if (! $enforceMfa) {
            return $next($request);
        }

        if (! $user->two_factor_confirmed_at) {
            return redirect()
                ->route('profile.edit')
                ->with('status', 'Please enable and confirm 2FA before accessing admin routes.');
        }

        return $next($request);
    }
}

