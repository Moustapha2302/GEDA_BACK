<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureServiceS01
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->canAccessS01()) {
            return response()->json(['message' => 'Accès interdit au service État Civil (S01)'], 403);
        }

        return $next($request);
    }
}
