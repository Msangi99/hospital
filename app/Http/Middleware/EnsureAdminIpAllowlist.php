<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIpAllowlist
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

        $enabled = (bool) config('admin-security.ip_allowlist.enabled', false);
        if (! $enabled) {
            return $next($request);
        }

        $ip = (string) ($request->ip() ?? '');
        $allowedIps = array_values(array_filter(
            (array) config('admin-security.ip_allowlist.allowed_ips', []),
            fn ($value) => is_string($value) && trim($value) !== ''
        ));

        if ($ip === '' || $allowedIps === [] || ! IpUtils::checkIp($ip, $allowedIps)) {
            abort(403, 'Admin access blocked for this IP address.');
        }

        return $next($request);
    }
}

