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
}
```

---

## 📋 Tests à effectuer dans Postman/Insomnia

### 1️⃣ **Chef Service S01** ✅
```
POST http://127.0.0.1:8000/api/login/s01
{
  "email": "chef.s01@ziguinchor.sn",
  "password": "123456"
}
```

### 2️⃣ **Maire**
```
POST http://127.0.0.1:8000/api/login/maire
{
  "email": "maire@ziguinchor.sn",
  "password": "123456"
}
```

### 3️⃣ **Secrétaire Général**
```
POST http://127.0.0.1:8000/api/login/sg
{
  "email": "sg@ziguinchor.sn",
  "password": "123456"
}
```

### 4️⃣ **Agent S01**
```
POST http://127.0.0.1:8000/api/login/agent-s01
{
  "email": "agent.s01@ziguinchor.sn",
  "password": "123456"
}
