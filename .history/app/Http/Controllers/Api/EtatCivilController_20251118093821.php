<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Acte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EtatCivilController extends Controller
{
    // Liste des actes
    public function index()
    {
        $actes = Acte::with(['createur', 'validePar'])->latest()->paginate(20);
        return response()->json($actes);
    }

    // Détail d’un acte
    public function show(Acte $acte)
    {
        return response()->json($acte->load(['createur', 'validePar']));
    }

    // Créer un nouvel acte
    public function store(Request $request)
    {
        try {
            Log::info('Tentative création acte', [
                'user_id' => auth()->id(),
                'data' => $request->all()
            ]);

            $validated = $request->validate([
                'numero_acte'      => 'required|string|unique:actes,numero_acte',
                'type'             => 'required|in:naissance,mariage,deces',
                'prenom'           => 'required|string|max:255',
                'nom'              => 'required|string|max:255',
                'date_naissance'   => 'required|date',
                'lieu_naissance'   => 'required|string|max:255',
                'sexe'             => 'required|in:M,F',
                'nom_pere'         => 'required|string|max:255',
                'nom_mere'         => 'required|string|max:255',
                'profession_pere'  => 'nullable|string|max:255',
            ]);

            $user = auth()->user();

            if (!$user) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }

            $donnees = [
                'prenom'          => $validated['prenom'],
                'nom'             => $validated['nom'],
                'date_naissance'  => $validated['date_naissance'],
                'lieu_naissance'   => $validated['lieu_naissance'],
                'sexe'            => $validated['sexe'],
                'nom_pere'        => $validated['nom_pere'],
                'nom_mere'        => $validated['nom_mere'],
                'profession_pere' => $validated['profession_pere'] ?? null,
            ];

            $acte = Acte::create([
                'numero_acte' => $validated['numero_acte'],
                'type'        => $validated['type'],
                'donnees'     => $donnees,
                'created_by'  => $user->id,
                'valide'      => false,
            ]);

            Log::info('Acte créé avec succès', ['acte_id' => $acte->id]);

            return response()->json([
                'message' => 'Acte créé avec succès',
                'acte'    => $acte->load('createur')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur création acte', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    // Modifier un acte non validé
    public function update(Request $request, Acte $acte)
    {
        try {
            $user = auth()->user();

            if ($acte->created_by !== $user->id) {
                return response()->json(['message' => 'Vous ne pouvez modifier que vos propres actes'], 403);
            }

            if ($acte->valide) {
                return response()->json(['message' => 'Impossible de modifier un acte validé'], 403);
            }

            $validated = $request->validate([
                'prenom'           => 'sometimes|string|max:255',
                'nom'              => 'sometimes|string|max:255',
                'date_naissance'   => 'sometimes|date',
                'lieu_naissance'   => 'sometimes|string|max:255',
                'sexe'             => 'sometimes|in:M,F',
                'nom_pere'         => 'sometimes|string|max:255',
                'nom_mere'         => 'sometimes|string|max:255',
                'profession_pere'  => 'nullable|string|max:255',
            ]);

            $donnees = array_merge($acte->donnees, $validated);
            $acte->update(['donnees' => $donnees]);

            return response()->json([
                'message' => 'Acte mis à jour avec succès',
                'acte'    => $acte->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour acte', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    // VALIDER UN ACTE – DÉCISION FINALE 18/11/2025
    public function valider(Acte $acte)
    {
        try {
            $user = auth()->user();

            // SEUL LE CHEF S01 OU LE SECRÉTAIRE GÉNÉRAL PEUT VALIDER
            if (! $user->peutValiderActesEtatCivil()) {
                return response()->json([
                    'message' => 'Seul le Chef du service État Civil ou le Secrétaire Général peut valider les actes.'
                ], 403);
            }

            if ($acte->valide) {
                return response()->json(['message' => 'Cet acte est déjà validé'], 400);
            }

            $acte->update([
                'valide'          => true,
                'valide_par'      => $user->id,
                'date_validation' => now(),
            ]);

            Log::info('Acte validé avec succès', [
                'acte_id'     => $acte->id,
                'validé_par'  => $user->name . ' (' . $user->role . ')'
            ]);

            return response()->json([
                'message' => 'Acte validé avec succès et désormais opposable aux tiers.',
                'acte'    => $acte->fresh(['createur', 'validePar'])
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur validation acte', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    // Recherche
    public function recherche(Request $request)
    {
        $q = $request->query('q');

        if (!$q) {
            return response()->json(['message' => 'Paramètre de recherche manquant'], 400);
        }

        $actes = Acte::where(function ($query) use ($q) {
            $query->where('numero_acte', 'like', "%$q%")
                ->orWhereRaw("JSON_EXTRACT(donnees, '$.nom') LIKE ?", ["%$q%"])
                ->orWhereRaw("JSON_EXTRACT(donnees, '$.prenom') LIKE ?", ["%$q%"]);
        })
            ->with(['createur'])
            ->get();

        return response()->json($actes);
    }

    // Générer certificat (placeholder)
    public function genererCertificat($type)
    {
        if (!in_array($type, ['naissance', 'mariage', 'deces'])) {
            return response()->json(['message' => 'Type de certificat invalide'], 400);
        }

        return response()->json([
            'message' => "Génération de certificat de $type en cours...",
            'type' => $type
        ]);
    }
}
