<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Connexion Chef Service État Civil (S01)
     */
    public function loginS01(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)
            ->where(function ($q) {
                $q->where('service_code', 'S01')
                    ->orWhereIn('role', ['sg', 'maire']);
            })
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects ou accès refusé à S01'], 401);
        }

        $token = $user->createToken('geda-s01-token', ['service:s01'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion S01 réussie',
            'user'    => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token'   => $token
        ]);
    }

    /**
     * Connexion Maire
     */
    public function loginMaire(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'maire')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects ou vous n\'êtes pas Maire'], 401);
        }

        $token = $user->createToken('geda-maire-token', ['role:maire'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion Maire réussie',
            'user'    => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token'   => $token
        ]);
    }

    /**
     * Connexion Secrétaire Général (SG)
     */
    public function loginSG(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'sg')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects ou vous n\'êtes pas SG'], 401);
        }

        $token = $user->createToken('geda-sg-token', ['role:sg'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion SG réussie',
            'user'    => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token'   => $token
        ]);
    }

    /**
     * Connexion Agent S01
     */
    public function loginAgentS01(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'agent')
            ->where('service_code', 'S01')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects ou vous n\'êtes pas Agent S01'], 401);
        }

        $token = $user->createToken('geda-agent-s01-token', ['service:s01', 'role:agent'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion Agent S01 réussie',
            'user'    => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token'   => $token
        ]);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté avec succès']);
    }

    // ================================================================
// MÉTHODES LOGIN POUR LE SERVICE FINANCE (S02)
// À ajouter dans App\Http\Controllers\Api\Auth\LoginController.php
// ================================================================

    /**
     * Connexion Contrôleur Financier
     */
    public function loginControleurFinancier(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $validated['email'])
            ->where('role', 'controleur_financier')
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect pour Contrôleur Financier'
            ], 401);
        }

        $token = $user->createToken('geda-controleur-financier-token', ['role:controleur_financier'])->plainTextToken;

        \Log::info('Contrôleur Financier connecté', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'message' => 'Connexion Contrôleur Financier réussie',
            'user' => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token' => $token
        ]);
    }

    /**
     * Connexion Chef Service Finance (S02)
     */
    public function loginChefS02(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $validated['email'])
            ->where('service_code', 'S02')
            ->whereIn('role', ['chef_s02', 'chef_service'])
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect pour Chef Finance'
            ], 401);
        }

        $token = $user->createToken('geda-chef-s02-token', ['service:s02', 'role:chef'])->plainTextToken;

        \Log::info('Chef Finance connecté', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]);

        return response()->json([
            'message' => 'Connexion Chef Finance réussie',
            'user' => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token' => $token
        ]);
    }

    /**
     * Connexion Agent Finance (S02)
     */
    public function loginAgentS02(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $validated['email'])
            ->where('service_code', 'S02')
            ->where('role', 'agent_s02')
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect pour Agent Finance'
            ], 401);
        }

        $token = $user->createToken('geda-agent-s02-token', ['service:s02', 'role:agent'])->plainTextToken;

        \Log::info('Agent Finance connecté', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'message' => 'Connexion Agent Finance réussie',
            'user' => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token' => $token
        ]);
    }

    /**
     * Connexion Services Techniques (accès lecture S02)
     */
    public function loginServicesTechniques(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Services techniques = S03, S04, S05, S06, S07, S08, S09, S10
        $servicesTechniques = ['S03', 'S04', 'S05', 'S06', 'S07', 'S08', 'S09', 'S10'];

        $user = User::where('email', $validated['email'])
            ->whereIn('service_code', $servicesTechniques)
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect pour Services Techniques'
            ], 401);
        }

        $token = $user->createToken('geda-services-techniques-token', [
            'service:' . strtolower($user->service_code),
            'access:s02:read' // Accès lecture uniquement au service Finance
        ])->plainTextToken;

        \Log::info('Service Technique connecté', [
            'user_id' => $user->id,
            'email' => $user->email,
            'service' => $user->service_code
        ]);

        return response()->json([
            'message' => 'Connexion Services Techniques réussie',
            'user' => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token' => $token,
            'acces' => 'Lecture seule sur documents financiers'
        ]);
    }

    /**
     * Connexion Autres Services (accès lecture S02)
     */
    public function loginAutresServices(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Autres services = S11, S12, S13
        $autresServices = ['S11', 'S12', 'S13'];

        $user = User::where('email', $validated['email'])
            ->whereIn('service_code', $autresServices)
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        $token = $user->createToken('geda-autres-services-token', [
            'service:' . strtolower($user->service_code),
            'access:s02:read' // Accès lecture uniquement
        ])->plainTextToken;

        \Log::info('Autre service connecté', [
            'user_id' => $user->id,
            'email' => $user->email,
            'service' => $user->service_code
        ]);

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token' => $token,
            'acces' => 'Lecture seule sur documents financiers'
        ]);
    }

    /**
     * Connexion générique Service Finance S02
     * (pour tout utilisateur S02 ou ayant accès transversal)
     */
    public function loginS02(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $validated['email'])
            ->where(function ($q) {
                $q->where('service_code', 'S02')
                    ->orWhereIn('role', ['sg', 'maire']); // Accès transversal
            })
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Identifiants incorrects ou accès refusé à S02'
            ], 401);
        }

        $abilities = ['service:s02'];
        if (in_array($user->role, ['maire', 'sg'])) {
            $abilities[] = 'role:' . $user->role;
            $abilities[] = 'access:all'; // Accès à tous les services
        }

        $token = $user->createToken('geda-s02-token', $abilities)->plainTextToken;

        \Log::info('Connexion S02', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]);

        return response()->json([
            'message' => 'Connexion S02 réussie',
            'user' => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token' => $token
        ]);
    }
}
