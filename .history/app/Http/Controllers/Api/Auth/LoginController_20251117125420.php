<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
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

        // Token avec ability claire
        $token = $user->createToken('geda-s01-token', ['service:s01'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion S01 réussie',
            'user'    => $user->only(['id', 'name', 'email', 'role', 'service_code']),
            'token'   => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté']);
    }
}
