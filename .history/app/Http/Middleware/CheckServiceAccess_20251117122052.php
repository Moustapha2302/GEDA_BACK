<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckServiceAccess
{
    public function handle(Request $request, Closure $next, string $serviceCode)
    {
        $user = $request->user();

        if (!$user || !$user->canAccessService($serviceCode)) {
            return response()->json(['message' => 'Accès refusé à ce service'], 403);
        }

        return $next($request);
    }
}
