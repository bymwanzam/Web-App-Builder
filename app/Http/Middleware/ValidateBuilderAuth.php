<?php

namespace App\Http\Middleware;

use App\Models\AppBuilder;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateBuilderAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get server_key from URL path parameter
        $serverKey = $request->route('server_key');

        if (! $serverKey) {
            return response()->json([
                'error' => 'Server key is required',
            ], 401);
        }

        // Validate server_key exists and belongs to an active builder
        $builder = AppBuilder::where('server_key', $serverKey)->first();

        if (! $builder) {
            return response()->json([
                'error' => 'Invalid server key',
            ], 401);
        }

        // Attach builder to request for use in controllers
        $request->attributes->set('app_builder', $builder);

        return $next($request);
    }
}
