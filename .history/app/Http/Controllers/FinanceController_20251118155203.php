<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentFinancier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class FinanceController extends Controller
{
    // Liste des documents financiers
    public function index()
    {
        $documents = DocumentFinancier::with(['createur', 'validePar'])
            ->latest()
            ->paginate(20);

        return response()->json($documents);
    }

    // Détail d'un document financier
    public function show(DocumentFinancier $document)
    {
        return response()->json($document->load(['createur', 'validePar']));
    }

    // Créer un nouveau document financier
    // FinanceController.php → méthode store() (remplace toute la fonction)
    public function store(Request $request)
    {
        try {
            Log::info('Création document financier', [
                'user_id' => auth()->id(),
                'payload' => $request->all()
            ]);

            $validated = $request->validate([
                'numero_document'  => 'required|string|unique:documents_financiers,numero_document',
                'type'             => 'required|in:facture,bon_commande,mandat,ordre_paiement,etat_depense,budget',
                'objet'            => 'required|string|max:500',
                'montant'          => 'required|numeric|min:0',
                'beneficiaire'     => 'required|string|max:255',
                'date_emission'    => 'required|date',
                'exercice'         => 'required|integer|min:2020|max:2100',
                'ligne_budgetaire' => 'nullable|string|max:100',
                'reference_marche' => 'nullable|string|max:100',
                'observations'     => 'nullable|string|max:1000',
            ]);

            $user = auth()->user();
            if (!$user) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }

            // Regroupe tout ce qui est spécifique dans le JSON donnees
            $donnees = [
                'objet'            => $validated['objet'],
                'montant'          => $validated['montant'],
                'beneficiaire'     => $validated['beneficiaire'],
                'date_emission'    => $validated['date_emission'],
                'exercice'         => $validated['exercice'],
                'ligne_budgetaire' => $validated['ligne_budgetaire'] ?? null,
                'reference_marche' => $validated['reference_marche'] ?? null,
                'observations'     => $validated['observations'] ?? null,
            ];

            $document = DocumentFinancier::create([
                'numero_document' => $validated['numero_document'],
                'type'            => $validated['type'],
                'donnees'         => $donnees,           // ← CORRIGÉ : "donnees" avec un "e"
                'created_by'      => $user->id,
                'valide'          => false,
            ]);

            return response()->json([
                'message'  => 'Document financier créé avec succès',
                'document' => $document->load('createur')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur création document financier', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // Modifier un document non validé
    public function update(Request $request, DocumentFinancier $document)
    {
        try {
            $user = auth()->user();

            if ($document->created_by !== $user->id) {
                return response()->json([
                    'message' => 'Vous ne pouvez modifier que vos propres documents'
                ], 403);
            }

            if ($document->valide) {
                return response()->json([
                    'message' => 'Impossible de modifier un document validé'
                ], 403);
            }

            $validated = $request->validate([
                'objet'            => 'sometimes|string|max:500',
                'montant'          => 'sometimes|numeric|min:0',
                'beneficiaire'     => 'sometimes|string|max:255',
                'date_emission'    => 'sometimes|date',
                'exercice'         => 'sometimes|integer|min:2020|max:2100',
                'ligne_budgetaire' => 'nullable|string|max:100',
                'reference_marche' => 'nullable|string|max:100',
                'observations'     => 'nullable|string|max:1000',
            ]);

            $donnees = array_merge($document->donnees, $validated);
            $document->update(['donnees' => $donnees]);

            return response()->json([
                'message' => 'Document mis à jour avec succès',
                'document' => $document->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour document', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    // VALIDER UN DOCUMENT FINANCIER
    public function valider(DocumentFinancier $document)
    {
        try {
            $user = auth()->user();

            // SEUL LE CHEF S02 OU LE SECRÉTAIRE GÉNÉRAL PEUT VALIDER
            if (!$user->peutValiderDocumentsFinanciers()) {
                return response()->json([
                    'message' => 'Seul le Chef du service Finance ou le Secrétaire Général peut valider les documents financiers.'
                ], 403);
            }

            if ($document->valide) {
                return response()->json(['message' => 'Ce document est déjà validé'], 400);
            }

            $document->update([
                'valide'          => true,
                'valide_par'      => $user->id,
                'date_validation' => now(),
            ]);

            Log::info('Document financier validé avec succès', [
                'document_id' => $document->id,
                'validé_par'  => $user->name . ' (' . $user->role . ')'
            ]);

            return response()->json([
                'message' => 'Document financier validé avec succès.',
                'document' => $document->fresh(['createur', 'validePar'])
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur validation document', ['error' => $e->getMessage()]);
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

        $documents = DocumentFinancier::where(function ($query) use ($q) {
            $query->where('numero_document', 'like', "%$q%")
                ->orWhereRaw("JSON_EXTRACT(donnees, '$.objet') LIKE ?", ["%$q%"])
                ->orWhereRaw("JSON_EXTRACT(donnees, '$.beneficiaire') LIKE ?", ["%$q%"]);
        })
            ->with(['createur'])
            ->get();

        return response()->json($documents);
    }

    // Statistiques financières
    public function statistiques(Request $request)
    {
        try {
            $exercice = $request->query('exercice', date('Y'));

            $stats = [
                'total_documents' => DocumentFinancier::whereRaw("JSON_EXTRACT(donnees, '$.exercice') = ?", [$exercice])->count(),
                'documents_valides' => DocumentFinancier::whereRaw("JSON_EXTRACT(donnees, '$.exercice') = ?", [$exercice])
                    ->where('valide', true)
                    ->count(),
                'montant_total' => DocumentFinancier::whereRaw("JSON_EXTRACT(donnees, '$.exercice') = ?", [$exercice])
                    ->where('valide', true)
                    ->get()
                    ->sum(function ($doc) {
                        return $doc->donnees['montant'] ?? 0;
                    }),
                'par_type' => DocumentFinancier::whereRaw("JSON_EXTRACT(donnees, '$.exercice') = ?", [$exercice])
                    ->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get()
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Erreur statistiques financières', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    // Rapport mensuel
    public function rapportMensuel(Request $request)
    {
        try {
            $mois = $request->query('mois', date('m'));
            $annee = $request->query('annee', date('Y'));

            $documents = DocumentFinancier::whereYear('created_at', $annee)
                ->whereMonth('created_at', $mois)
                ->with(['createur', 'validePar'])
                ->get();

            $rapport = [
                'periode' => "$mois/$annee",
                'total_documents' => $documents->count(),
                'documents_valides' => $documents->where('valide', true)->count(),
                'montant_total' => $documents->where('valide', true)->sum(function ($doc) {
                    return $doc->donnees['montant'] ?? 0;
                }),
                'documents' => $documents
            ];

            return response()->json($rapport);
        } catch (\Exception $e) {
            Log::error('Erreur génération rapport', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }
}
