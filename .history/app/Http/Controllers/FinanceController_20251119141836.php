<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\PieceComptable;
use App\Models\BonCommande;
use App\Models\MarchePublic;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    // ==================== BUDGETS ====================

    /**
     * Liste des budgets
     * Permissions: Contrôleur Financier, Chef S02, SG, Maire
     */
    public function getBudgets(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['Contrôleur Financier', 'Chef S02', 'SG', 'Maire'])) {
            return response()->json([
                'message' => 'Accès refusé. Rôles autorisés: Contrôleur Financier, Chef S02, SG, Maire'
            ], 403);
        }

        $budgets = Budget::with('service')->get();

        return response()->json([
            'budgets' => $budgets,
            'total' => $budgets->count()
        ], 200);
    }

    /**
     * Budget d'un service spécifique
     * Permissions: Chef du service concerné ou rôles supérieurs
     */
    public function getBudgetService(Request $request, $service)
    {
        $user = $request->user();

        \Log::info("=== DEBUG getBudgetService ===");
        \Log::info("User: " . $user->email . " | Role: " . $user->role . " | Service: " . $user->service);
        \Log::info("Service demandé: " . $service);
        \Log::info("Roles autorisés: " . json_encode(['Contrôleur Financier', 'Chef S02', 'SG', 'Maire']));

        // Vérifier les permissions
        $rolesAutorises = ['Contrôleur Financier', 'Chef S02', 'SG', 'Maire'];

        if (!in_array($user->role, $rolesAutorises)) {
            \Log::warning("Role non autorisé: " . $user->role);
            // Le chef du service peut voir son propre budget
            if ($user->service !== $service) {
                \Log::warning("Accès refusé - Service différent: " . $user->service . " vs " . $service);
                return response()->json([
                    'message' => 'Accès refusé'
                ], 403);
            }
        }

        \Log::info("Accès autorisé, recherche du budget...");
        $budget = Budget::where('service', $service)->first();

        if (!$budget) {
            \Log::warning("Budget non trouvé pour service: " . $service);
            return response()->json([
                'message' => 'Budget non trouvé'
            ], 404);
        }

        \Log::info("Budget trouvé: " . $budget->id);
        return response()->json($budget, 200);
    }
    // ==================== PIÈCES COMPTABLES ====================

    /**
     * Liste des pièces comptables
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = PieceComptable::query();

        // Filtrer selon le rôle
        if ($user->role === 'Agent S02') {
            // L'agent ne voit que ses propres pièces
            $query->where('createur_id', $user->id);
        }

        $pieces = $query->with(['createur', 'validateur'])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'pieces_comptables' => $pieces,
            'total' => $pieces->count()
        ], 200);
    }

    /**
     * Détail d'une pièce comptable
     */
    public function show(Request $request, $id)
    {
        $piece = PieceComptable::with(['createur', 'validateur'])->find($id);

        if (!$piece) {
            return response()->json([
                'message' => 'Pièce comptable non trouvée'
            ], 404);
        }

        return response()->json($piece, 200);
    }

    /**
     * Créer une pièce comptable
     * Permissions: Agent S02, Chef S02
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['Agent S02', 'Chef S02'])) {
            return response()->json([
                'message' => 'Accès refusé. Rôles autorisés: Agent S02, Chef S02'
            ], 403);
        }

        $validated = $request->validate([
            'type' => 'required|string|in:Mandat,Titre,Ordre de Recette,Engagement',
            'numero' => 'required|string|unique:pieces_comptables',
            'montant' => 'required|numeric|min:0',
            'description' => 'required|string',
            'service_beneficiaire' => 'required|string',
        ]);

        $piece = PieceComptable::create([
            'type' => $validated['type'],
            'numero' => $validated['numero'],
            'montant' => $validated['montant'],
            'description' => $validated['description'],
            'service_beneficiaire' => $validated['service_beneficiaire'],
            'statut' => 'Brouillon',
            'createur_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Pièce comptable créée avec succès',
            'piece' => $piece->load('createur')
        ], 201);
    }

    /**
     * Modifier une pièce comptable
     * Permissions: Agent S02 (propriétaire), Chef S02
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $piece = PieceComptable::find($id);

        if (!$piece) {
            return response()->json([
                'message' => 'Pièce comptable non trouvée'
            ], 404);
        }

        if ($piece->statut !== 'Brouillon') {
            return response()->json([
                'message' => 'Seules les pièces en brouillon peuvent être modifiées'
            ], 400);
        }

        // Vérifier les permissions
        if ($user->role === 'Agent S02' && $piece->createur_id !== $user->id) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier que vos propres pièces'
            ], 403);
        }

        if (!in_array($user->role, ['Agent S02', 'Chef S02'])) {
            return response()->json([
                'message' => 'Accès refusé'
            ], 403);
        }

        $validated = $request->validate([
            'type' => 'sometimes|string|in:Mandat,Titre,Ordre de Recette,Engagement',
            'numero' => 'sometimes|string|unique:pieces_comptables,numero,' . $id,
            'montant' => 'sometimes|numeric|min:0',
            'description' => 'sometimes|string',
            'service_beneficiaire' => 'sometimes|string',
        ]);

        $piece->update($validated);

        return response()->json([
            'message' => 'Pièce comptable modifiée avec succès',
            'piece' => $piece->load('createur')
        ], 200);
    }

    /**
     * Valider une pièce comptable
     * Permissions: Contrôleur Financier uniquement
     */
    public function valider(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'Contrôleur Financier') {
            return response()->json([
                'message' => 'Seul le Contrôleur Financier peut valider les pièces'
            ], 403);
        }

        $piece = PieceComptable::find($id);

        if (!$piece) {
            return response()->json([
                'message' => 'Pièce comptable non trouvée'
            ], 404);
        }

        if ($piece->statut !== 'Brouillon') {
            return response()->json([
                'message' => 'Cette pièce a déjà été validée'
            ], 400);
        }

        $piece->update([
            'statut' => 'Validée',
            'validateur_id' => $user->id,
            'date_validation' => now(),
        ]);

        return response()->json([
            'message' => 'Pièce comptable validée avec succès',
            'piece' => $piece->load(['createur', 'validateur'])
        ], 200);
    }

    // ==================== BONS DE COMMANDE ====================

    /**
     * Liste des bons de commande
     */
    public function getBonsCommande(Request $request)
    {
        $bons = BonCommande::with(['createur', 'viseur'])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'bons_commande' => $bons,
            'total' => $bons->count()
        ], 200);
    }

    /**
     * Créer un bon de commande
     */
    public function createBonCommande(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'numero' => 'required|string|unique:bons_commande',
            'objet' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'fournisseur' => 'required|string',
        ]);

        $bon = BonCommande::create([
            'numero' => $validated['numero'],
            'objet' => $validated['objet'],
            'montant' => $validated['montant'],
            'fournisseur' => $validated['fournisseur'],
            'service_demandeur' => $user->service,
            'statut' => 'En attente',
            'createur_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Bon de commande créé avec succès',
            'bon' => $bon->load('createur')
        ], 201);
    }

    /**
     * Viser un bon de commande
     * Permissions: Contrôleur Financier uniquement
     */
    public function viserBonCommande(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'Contrôleur Financier') {
            return response()->json([
                'message' => 'Seul le Contrôleur Financier peut viser les bons de commande'
            ], 403);
        }

        $bon = BonCommande::find($id);

        if (!$bon) {
            return response()->json([
                'message' => 'Bon de commande non trouvé'
            ], 404);
        }

        if ($bon->statut !== 'En attente') {
            return response()->json([
                'message' => 'Ce bon a déjà été visé'
            ], 400);
        }

        $validated = $request->validate([
            'avis' => 'required|string|in:Favorable,Défavorable',
            'observations' => 'nullable|string',
        ]);

        $bon->update([
            'statut' => $validated['avis'] === 'Favorable' ? 'Visé' : 'Rejeté',
            'viseur_id' => $user->id,
            'date_visa' => now(),
            'avis' => $validated['avis'],
            'observations' => $validated['observations'] ?? '',
        ]);

        return response()->json([
            'message' => "Bon de commande {$bon->statut} avec succès",
            'bon' => $bon->load(['createur', 'viseur'])
        ], 200);
    }

    // ==================== MARCHÉS PUBLICS ====================

    /**
     * Liste des marchés publics
     */
    public function getMarchesPublics(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['Contrôleur Financier', 'Chef S02', 'SG', 'Maire'])) {
            return response()->json([
                'message' => 'Accès refusé'
            ], 403);
        }

        $marches = MarchePublic::with(['viseur'])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'marches' => $marches,
            'total' => $marches->count()
        ], 200);
    }

    /**
     * Viser un marché public
     * Permissions: Contrôleur Financier uniquement
     */
    public function viserMarchePublic(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'Contrôleur Financier') {
            return response()->json([
                'message' => 'Seul le Contrôleur Financier peut viser les marchés publics'
            ], 403);
        }

        $marche = MarchePublic::find($id);

        if (!$marche) {
            return response()->json([
                'message' => 'Marché public non trouvé'
            ], 404);
        }

        if ($marche->statut !== 'En attente') {
            return response()->json([
                'message' => 'Ce marché a déjà été visé'
            ], 400);
        }

        $validated = $request->validate([
            'avis' => 'required|string|in:Favorable,Défavorable',
            'observations' => 'nullable|string',
        ]);

        $marche->update([
            'statut' => $validated['avis'] === 'Favorable' ? 'Visé' : 'Rejeté',
            'viseur_id' => $user->id,
            'date_visa' => now(),
            'avis' => $validated['avis'],
            'observations' => $validated['observations'] ?? '',
        ]);

        return response()->json([
            'message' => "Marché public {$marche->statut} avec succès",
            'marche' => $marche->load('viseur')
        ], 200);
    }

    // ==================== RAPPORTS ====================

    /**
     * Rapport budgétaire
     * Permissions: Contrôleur Financier, Chef S02, SG, Maire
     */
    public function rapportBudget(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['Contrôleur Financier', 'Chef S02', 'SG', 'Maire'])) {
            return response()->json([
                'message' => 'Accès refusé'
            ], 403);
        }

        $budgets = Budget::all();
        $pieces = PieceComptable::all();

        $budgetTotal = $budgets->sum('budget_initial');
        $engageTotal = $budgets->sum('engage');
        $disponibleTotal = $budgets->sum('disponible');

        $rapport = [
            'date_generation' => now()->toISOString(),
            'generateur' => $user->email,
            'budgets' => $budgets,
            'statistiques' => [
                'budget_total' => $budgetTotal,
                'engage_total' => $engageTotal,
                'disponible_total' => $disponibleTotal,
                'taux_execution' => $budgetTotal > 0 ? round(($engageTotal / $budgetTotal) * 100, 2) : 0,
            ],
            'pieces_comptables' => [
                'total' => $pieces->count(),
                'validees' => $pieces->where('statut', 'Validée')->count(),
                'brouillons' => $pieces->where('statut', 'Brouillon')->count(),
            ]
        ];

        return response()->json($rapport, 200);
    }

    /**
     * Recherche
     */
    public function recherche(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all');

        $results = [];

        if ($type === 'all' || $type === 'pieces') {
            $results['pieces_comptables'] = PieceComptable::where('numero', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->get();
        }

        if ($type === 'all' || $type === 'bons') {
            $results['bons_commande'] = BonCommande::where('numero', 'like', "%{$query}%")
                ->orWhere('objet', 'like', "%{$query}%")
                ->get();
        }

        return response()->json($results, 200);
    }

    /**
     * Statistiques
     */
    public function statistiques(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['Chef S02', 'SG', 'Contrôleur Financier'])) {
            return response()->json([
                'message' => 'Accès refusé'
            ], 403);
        }

        $stats = [
            'pieces_comptables' => [
                'total' => PieceComptable::count(),
                'validees' => PieceComptable::where('statut', 'Validée')->count(),
                'brouillons' => PieceComptable::where('statut', 'Brouillon')->count(),
            ],
            'bons_commande' => [
                'total' => BonCommande::count(),
                'vises' => BonCommande::where('statut', 'Visé')->count(),
                'en_attente' => BonCommande::where('statut', 'En attente')->count(),
            ],
            'budget' => [
                'total' => Budget::sum('budget_initial'),
                'engage' => Budget::sum('engage'),
                'disponible' => Budget::sum('disponible'),
            ]
        ];

        return response()->json($stats, 200);
    }
}
