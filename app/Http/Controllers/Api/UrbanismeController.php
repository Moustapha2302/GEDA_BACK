<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermisConstruire;
use App\Models\PlanOccupationSol;
use App\Models\Autorisation;
use Illuminate\Support\Facades\Log;

class UrbanismeController extends Controller
{
    // ============================================
    // PERMIS DE CONSTRUIRE
    // ============================================

    /**
     * Liste des permis de construire
     */
    public function indexPermis(Request $request)
    {
        try {
            $permis = PermisConstruire::with(['createur:id,nom,prenom', 'viseur:id,nom,prenom'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des permis de construire',
                'data' => $permis
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur récupération permis', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des permis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un permis de construire
     */
    public function storePermis(Request $request)
    {
        try {
            $validated = $request->validate([
                'numero' => 'required|string|unique:permis_construire,numero',
                'nom_demandeur' => 'required|string|max:255',
                'prenom_demandeur' => 'required|string|max:255',
                'adresse_terrain' => 'required|string',
                'superficie' => 'required|numeric|min:0',
                'type_projet' => 'required|string|max:255',
            ]);

            $validated['createur_id'] = auth()->id();
            $validated['statut'] = 'Brouillon';

            $permis = PermisConstruire::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Permis de construire créé avec succès',
                'data' => $permis
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur création permis', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du permis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Détail d’un permis
     */
    public function showPermis(Request $request, $id)
    {
        try {
            $permis = PermisConstruire::with(['createur:id,nom,prenom', 'viseur:id,nom,prenom'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Détail du permis de construire',
                'data' => $permis
            ], 200);
        } catch (\Exception $e) {
            Log::error('Permis introuvable', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Permis non trouvé',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Modifier un permis (non validé)
     */
    public function updatePermis(Request $request, $id)
    {
        try {
            $permis = PermisConstruire::where('statut', 'Brouillon')->findOrFail($id);

            // Vérifier que l'utilisateur est le créateur ou chef du service
            if (auth()->id() !== $permis->createur_id && auth()->user()->role !== 'chef_service') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à modifier ce permis'
                ], 403);
            }

            $validated = $request->validate([
                'nom_demandeur' => 'sometimes|string|max:255',
                'prenom_demandeur' => 'sometimes|string|max:255',
                'adresse_terrain' => 'sometimes|string',
                'superficie' => 'sometimes|numeric|min:0',
                'type_projet' => 'sometimes|string|max:255',
            ]);

            $permis->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Permis modifié avec succès',
                'data' => $permis
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur modification permis', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du permis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Demander avis juridique
     */
    public function demanderAvisJuridique(Request $request, $id)
    {
        \Log::info('=== DEMANDE AVIS JURIDIQUE ===');
        \Log::info('Utilisateur connecté', [
            'id' => auth()->id(),
            'role' => auth()->user()->role,
            'service' => auth()->user()->service_code,
        ]);
        \Log::info('Corps reçu', $request->all());
        \Log::info('Headers', $request->headers->all());

        try {
            $permis = PermisConstruire::findOrFail($id);

            if (auth()->user()->role !== 'chef_service' || auth()->user()->service_code !== 'S03') {
                return response()->json([
                    'success' => false,
                    'message' => 'Seul le chef du service peut demander un avis juridique'
                ], 403);
            }

            $validated = $request->validate([
                'observations' => 'required|string',
            ]);

            $permis->update([
                'observations' => $validated['observations'],
                'statut' => 'En attente d\'avis juridique'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avis juridique demandé avec succès',
                'data' => $permis
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur demande avis juridique', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la demande d\'avis juridique',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valider un permis (Chef S03, SG, Maire)
     */
    public function validerPermis(Request $request, $id)
    {
        try {
            $permis = PermisConstruire::findOrFail($id);

            $validateurs = ['chef_service', 'sg', 'maire'];
            if (!in_array(auth()->user()->role, $validateurs)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à valider ce permis'
                ], 403);
            }

            $permis->update([
                'statut' => 'Visé',
                'viseur_id' => auth()->id(),
                'date_visa' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permis visé avec succès',
                'data' => $permis
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur validation permis', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation du permis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // PLANS D'OCCUPATION DES SOLS
    // ============================================

    public function indexPlans(Request $request)
    {
        $plans = PlanOccupationSol::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des plans d’occupation des sols',
            'data' => $plans
        ], 200);
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'fichier' => 'required|string', // ou file upload
            'zone' => 'required|string|max:255',
        ]);

        $validated['createur_id'] = auth()->id();

        $plan = PlanOccupationSol::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Plan d’occupation créé avec succès',
            'data' => $plan
        ], 201);
    }

    // ============================================
    // AUTORISATIONS DIVERSES
    // ============================================

    public function indexAutorisations(Request $request)
    {
        $autorisations = Autorisation::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des autorisations diverses',
            'data' => $autorisations
        ], 200);
    }

    // ============================================
    // SUIVI DOSSIER CITOYEN
    // ============================================

    public function suiviDossierCitoyen(Request $request, $id)
    {
        // Vérifie que le demandeur est bien le citoyen concerné
        if (auth()->id() != $id && !in_array(auth()->user()->role, ['sg', 'maire'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez consulter que vos propres dossiers'
            ], 403);
        }

        $permis = PermisConstruire::where('createur_id', $id)->get();
        $autorisations = Autorisation::where('createur_id', $id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Suivi des dossiers du citoyen',
            'data' => [
                'permis' => $permis,
                'autorisations' => $autorisations
            ]
        ], 200);
    }
}
