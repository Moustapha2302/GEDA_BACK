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

        $abilities = ['service:s01'];
        if (in_array($user->role, ['maire', 'sg'])) {
            $abilities[] = 'role:' . $user->role;
            $abilities[] = 'access:all';
        }

        $token = $user->createToken('geda-s01-token', $abilities)->plainTextToken;

        \Log::info('Connexion S01 réussie', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]);

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

        $token = $user->createToken('geda-maire-token', ['role:maire', 'access:all'])->plainTextToken;

        \Log::info('Connexion Maire réussie', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

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

        $token = $user->createToken('geda-sg-token', ['role:sg', 'access:all'])->plainTextToken;

        \Log::info('Connexion SG réussie', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

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

        \Log::info('Connexion Agent S01 réussie', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'message' => 'Connexion Agent S01 réussie',
            'user'    => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token'   => $token
        ]);
    }

    /**
     * Login pour les agents du Service Finance (S02)
     */
    public function loginAgentS02(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email incorrect ou vous n\'êtes pas Agent Finance'
            ], 401);
        }

        // Vérifier que l'utilisateur appartient au service S02
        if ($user->service !== 'S02') {
            return response()->json([
                'message' => 'Email incorrect ou vous n\'êtes pas Agent Finance'
            ], 401);
        }

        // Vérifier le rôle (Agent S02)
        if (!in_array($user->role, ['Agent S02', 'Chef S02'])) {
            return response()->json([
                'message' => 'Email incorrect ou vous n\'êtes pas Agent Finance'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect'
            ], 401);
        }

        $token = $user->createToken('auth_token', ['service:S02'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role,
                'service' => $user->service,
            ]
        ], 200);
    }

    /**
     * Login pour le Contrôleur Financier
     */
    public function loginControleurFinancier(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->role !== 'Contrôleur Financier') {
            return response()->json([
                'message' => 'Email incorrect ou vous n\'êtes pas Contrôleur Financier'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect'
            ], 401);
        }

        $token = $user->createToken('auth_token', ['service:S02', 'role:controleur-financier'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role,
                'service' => $user->service,
            ]
        ], 200);
    }

    /**
     * Login pour le Chef S02
     */
    public function loginChefS02(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->role !== 'Chef S02') {
            return response()->json([
                'message' => 'Email incorrect ou vous n\'êtes pas Chef S02'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect'
            ], 401);
        }

        $token = $user->createToken('auth_token', ['service:S02', 'role:chef-s02'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role,
                'service' => $user->service,
            ]
        ], 200);
    }

    /**
     * Login générique pour S02 (SG, Maire, etc.)
     */
    public function loginS02(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email incorrect'
            ], 401);
        }

        // Vérifier les rôles autorisés pour S02
        $rolesAutorises = ['SG', 'Maire', 'Chef S02', 'Agent S02', 'Contrôleur Financier'];

        if (!in_array($user->role, $rolesAutorises)) {
            return response()->json([
                'message' => 'Accès non autorisé au service Finance'
            ], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect'
            ], 401);
        }

        $abilities = ['service:S02'];
        if (in_array($user->role, ['SG', 'Maire'])) {
            $abilities[] = 'role:admin';
        }

        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role,
                'service' => $user->service,
            ]
        ], 200);
    }

    /**
     * Login pour Services Techniques
     */
    public function loginServicesTechniques(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->service !== 'Services Techniques') {
            return response()->json([
                'message' => 'Email incorrect ou vous n\'appartenez pas aux Services Techniques'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect'
            ], 401);
        }

        $token = $user->createToken('auth_token', ['consultation:finance'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role,
                'service' => $user->service,
            ]
        ], 200);
    }

    /**
     * Login pour Autres Services
     */
    public function loginAutresServices(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email incorrect'
            ], 401);
        }

        // Exclure les services spéciaux
        $servicesExclus = ['S01', 'S02', 'Services Techniques', 'ADMINISTRATION'];

        if (in_array($user->service, $servicesExclus)) {
            return response()->json([
                'message' => 'Utilisez l\'endpoint de connexion approprié pour votre service'
            ], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect'
            ], 401);
        }

        $token = $user->createToken('auth_token', ['consultation:finance'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role,
                'service' => $user->service,
            ]
        ], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ], 200);
    }

    public function loginChefS03(Request $request)
    {
        return $this->login($request, 'chef_service', 'S03');
    }

    public function loginAgentS03(Request $request)
    {
        return $this->login($request, 'agent', 'S03');
    }

    /**
     * Méthode générique de connexion par rôle et service
     */
    private function login(Request $request, string $role, string $service)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)
            ->where(function ($q) use ($role, $service) {
                $q->where('role', $role)
                    ->where('service_code', $service);
            })
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => "Identifiants incorrects ou accès refusé à $service"
            ], 401);
        }

        $token = $user->createToken("geda-{$service}-token", ["service:$service", "role:$role"])->plainTextToken;

        \Log::info("Connexion $service réussie", [
            'user_id' => $user->id,
            'email'   => $user->email,
            'role'    => $user->role
        ]);

        return response()->json([
            'message' => "Connexion $service réussie",
            'user'    => $user->only(['id', 'nom', 'prenom', 'email', 'role', 'service_code']),
            'token'   => $token
        ], 200);
    }
}
