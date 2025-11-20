<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
{
    /**
     * Liste des budgets
     * Accessible par : Contrôleur Financier, Chef S02, SG, Maire
     */
    public function getBudgets(Request $request)
    {
        try {
            Log::info('Tentative de récupération des budgets', [
                'user_id' => $request->user()->id,
                'role' => $request->user()->role,
                'service' => $request->user()->service ?? 'non défini'
            ]);

            // Récupérer tous les budgets
            $budgets = Budget::all();

            Log::info('Budgets récupérés avec succès', [
                'count' => $budgets->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $budgets
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des budgets', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des budgets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Budget d'un service spécifique
     */
    public function getBudgetService(Request $request, $service)
    {
        try {
            $budget = Budget::where('service', $service)
                ->where('annee', date('Y'))
                ->first();

            if (!$budget) {
                return response()->json([
                    'success' => false,
                    'message' => 'Budget non trouvé pour ce service'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $budget
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur getBudgetService', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du budget',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste des pièces comptables
     */
    /**
     * Liste des pièces comptables
     */
    public function index(Request $request)
    {
        try {
            $pieces = \App\Models\PieceComptable::all();

            return response()->json([
                'success' => true,
                'message' => 'Liste des pièces comptables',
                'data' => $pieces
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des pièces comptables', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des pièces comptables',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Détail d'une pièce comptable
     */
    public function show(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Détail de la pièce comptable #' . $id
        ], 200);
    }

    /**
     * Créer une pièce comptable
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Pièce comptable créée'
        ], 201);
    }

    /**
     * Modifier une pièce comptable
     */
    public function update(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Pièce comptable mise à jour'
        ], 200);
    }

    /**
     * Valider une pièce comptable
     */
    public function valider(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Pièce comptable validée'
        ], 200);
    }

    /**
     * Liste des bons de commande
     */
    public function getBonsCommande(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste des bons de commande',
            'data' => []
        ], 200);
    }

    /**
     * Créer un bon de commande
     */
    public function createBonCommande(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Bon de commande créé'
        ], 201);
    }

    /**
     * Viser un bon de commande
     */
    public function viserBonCommande(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Bon de commande visé'
        ], 200);
    }

    /**
     * Liste des marchés publics
     */
    public function getMarchesPublics(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste des marchés publics',
            'data' => []
        ], 200);
    }

    /**
     * Viser un marché public
     */
    public function viserMarchePublic(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Marché public visé'
        ], 200);
    }

    /**
     * Rapport budgétaire
     */
    public function rapportBudget(Request $request)
    {
        $stats = [
            'total_budgets' => Budget::sum('budget_initial'),
            'total_engage' => Budget::sum('engage'),
            'total_disponible' => Budget::sum('disponible'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Rapport budgétaire',
            'data' => $stats
        ], 200);
    }

    /**
     * Recherche
     */
    public function recherche(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Résultats de recherche',
            'data' => []
        ], 200);
    }

    /**
     * Statistiques
     */
    public function statistiques(Request $request)
    {
        $stats = [
            'total_budgets' => Budget::sum('budget_initial'),
            'total_engage' => Budget::sum('engage'),
            'total_disponible' => Budget::sum('disponible'),
            'nombre_services' => Budget::distinct('service')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ], 200);
    }
}
