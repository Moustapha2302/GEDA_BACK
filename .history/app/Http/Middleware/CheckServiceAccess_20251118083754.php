<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckServiceAccess
{
    /**
     * Vérifie si l'utilisateur a accès au service demandé
     *
     * @param Request $request
     * @param Closure $next
     * @param string $serviceCode Le code du service (ex: S01, S02, etc.)
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $serviceCode): Response
    {
        $user = $request->user();

        // Vérifier l'authentification
        if (!$user) {
            return response()->json([
                'message' => 'Non authentifié'
            ], 401);
        }

        // ✅ Maire, SG et Directeur de Cabinet ont accès à tous les services
        if (in_array($user->role, ['maire', 'sg', 'directeur_cabinet'])) {
            return $next($request);
        }

        // ✅ Vérification du service (insensible à la casse)
        $userServiceCode = strtoupper($user->service_code ?? '');
        $requiredServiceCode = strtoupper($serviceCode);

        if ($userServiceCode !== $requiredServiceCode) {
            return response()->json([
                'message' => "Accès refusé. Seul le service {$requiredServiceCode} peut accéder à cette ressource.",
                'votre_service' => $user->service_code ?? 'aucun',
                'service_requis' => $serviceCode
            ], 403);
        }

        return $next($request);
    }
}
