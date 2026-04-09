<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = ['en', 'fr', 'ar', 'sw'];
        $locale = (string) $request->session()->get('locale', config('app.locale', 'en'));

        if (! in_array($locale, $allowed, true)) {
            $locale = (string) config('app.fallback_locale', 'en');
        }

        app()->setLocale($locale);

        view()->share('currentLocale', $locale);

        return $next($request);
    }
}

