<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckServiceAccess
{
    public function handle(Request $request, Closure $next, string $serviceCode): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Maire, SG, Directeur de Cabinet = accès total
        if (in_array($user->role, ['maire', 'sg', 'directeur_cabinet'])) {
            return $next($request);
        }

        // Vérification du service (insensible à la casse)
        if (strtoupper($user->service_code ?? '') !== strtoupper($serviceCode)) {
            return response()->json([
                'message' => "Accès refusé. Vous n'appartenez pas au service {$serviceCode}",
                'votre_service' => $user->service_code ?? 'aucun',
                'service_requis' => $serviceCode
            ], 403);
        }

        return $next($request);
    }
}
