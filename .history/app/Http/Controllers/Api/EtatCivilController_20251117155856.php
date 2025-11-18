<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Acte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EtatCivilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $actes = Acte::with(['createur', 'validePar'])
            ->where('service_id', 1)
            ->latest()
            ->paginate(20);

        return response()->json($actes);
    }

    public function show(Acte $acte)
    {
        return response()->json($acte->load(['createur', 'validePar']));
    }

    public function store(Request $request)
    {
        try {
            // Validation
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

            // Vérification utilisateur
            $user = auth()->user();

            if (!$user || $user->service_id != 1) {
                return response()->json([
                    'message' => 'Accès refusé. Service non autorisé.'
                ], 403);
            }

            // Préparer les données pour le champ JSON
            $donnees = [
                'prenom'          => $validated['prenom'],
                'nom'             => $validated['nom'],
                'date_naissance'  => $validated['date_naissance'],
                'lieu_naissance'  => $validated['lieu_naissance'],
                'sexe'            => $validated['sexe'],
                'nom_pere'        => $validated['nom_pere'],
                'nom_mere'        => $validated['nom_mere'],
                'profession_pere' => $validated['profession_pere'] ?? null,
            ];

            // Création de l'acte
            $acte = Acte::create([
                'numero_acte' => $validated['numero_acte'],
                'type'        => $validated['type'],
                'donnees'     => $donnees,
                'created_by'  => $user->id,
                'service_id'  => 1,
                'valide'      => false,
            ]);

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
            Log::error('Erreur création acte', [
                'user_id'   => auth()->id(),
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erreur serveur',
                'error'   => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue'
            ], 500);
        }
    }

    public function update(Request $request, Acte $acte)
    {
        try {
            // Vérifier que l'acte n'est pas déjà validé
            if ($acte->valide) {
                return response()->json([
                    'message' => 'Impossible de modifier un acte validé'
                ], 403);
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

            // Fusionner les anciennes données avec les nouvelles
            $donnees = array_merge($acte->donnees, $validated);

            $acte->update(['donnees' => $donnees]);

            return response()->json([
                'message' => 'Acte mis à jour avec succès',
                'acte'    => $acte->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour acte', [
                'acte_id' => $acte->id,
                'error'   => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }

    public function valider(Acte $acte)
    {
        try {
            $user = auth()->user();

            // Vérifier que l'utilisateur peut valider
            if ($user->service_id != 1) {
                return response()->json([
                    'message' => 'Seul le service État Civil peut valider'
                ], 403);
            }

            if ($acte->valide) {
                return response()->json([
                    'message' => 'Cet acte est déjà validé'
                ], 400);
            }

            $acte->update([
                'valide'          => true,
                'valide_par'      => $user->id,
                'date_validation' => now()
            ]);

            return response()->json([
                'message' => 'Acte validé avec succès',
                'acte'    => $acte->fresh(['createur', 'validePar'])
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur validation acte', [
                'acte_id' => $acte->id,
                'error'   => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Erreur lors de la validation'
            ], 500);
        }
    }

    public function recherche(Request $request)
    {
        $q = $request->query('q');

        if (!$q) {
            return response()->json([
                'message' => 'Paramètre de recherche manquant'
            ], 400);
        }

        $actes = Acte::where('service_id', 1)
            ->where(function ($query) use ($q) {
                $query->where('numero_acte', 'like', "%$q%")
                    ->orWhereRaw("JSON_EXTRACT(donnees, '$.nom') LIKE ?", ["%$q%"])
                    ->orWhereRaw("JSON_EXTRACT(donnees, '$.prenom') LIKE ?", ["%$q%"]);
            })
            ->with(['createur'])
            ->get();

        return response()->json($actes);
    }

    public function genererCertificat($type)
    {
        // À implémenter selon vos besoins
        return response()->json([
            'message' => "Génération de certificat de $type en cours..."
        ]);
    }
}
