<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotInstalled
{
    /**
     * Handle an incoming request.
     *
     * Only allow access to installer routes if installation is not complete
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to completed page even after installation
        if ($request->routeIs('install.completed')) {
            return $next($request);
        }

        if ($this->isInstalled()) {
            return redirect()->route('login');
        }

        return $next($request);
    }

    /**
     * Check if application is installed
     */
    private function isInstalled(): bool
    {
        try {
            return SystemSetting::get('installation_completed', false) === true;
        } catch (\Exception $e) {
            // If we can't check (database not set up yet), assume not installed
            return false;
        }
    }
}
