<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PieceComptable;
use App\Models\BonCommande;
use App\Models\MarchePublic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    // === PIÈCES COMPTABLES ===
    public function indexPieces()
    {
        $user = Auth::user();
        $query = PieceComptable::with('createur', 'validateur');

        // Agent S02 ne voit que ses propres pièces
        if ($user->role === 'Agent S02') {
            $query->where('createur_id', $user->id);
        }

        return response()->json($query->latest()->get());
    }

    public function storePiece(Request $request)
    {
        $user = Auth::user();

        // Seuls Agent S02 et Chef S02 peuvent créer
        if (!in_array($user->role, ['Agent S02', 'Chef S02'])) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $data = $request->validate([
            'type' => 'required|in:Mandat,Titre,Ordre de Recette,Engagement',
            'numero' => 'required|string|unique:pieces_comptables,numero',
            'montant' => 'required|numeric|min:0',
            'description' => 'required|string',
            'service_beneficiaire' => 'required|string|size:3',
        ]);

        $piece = PieceComptable::create($data);

        return response()->json([
            'message' => 'Pièce créée avec succès',
            'piece' => $piece->load('createur')
        ], 201);
    }

    public function updatePiece(Request $request, PieceComptable $piece)
    {
        $user = Auth::user();

        // Seules ses propres pièces en brouillon
        if ($piece->createur_id !== $user->id || $piece->statut !== 'Brouillon') {
            return response()->json(['error' => 'Modification interdite'], 403);
        }

        $data = $request->validate([
            'montant' => 'sometimes|numeric',
            'description' => 'sometimes|string',
        ]);

        $piece->update($data);

        return response()->json($piece);
    }

    public function validerPiece(PieceComptable $piece)
    {
        if (Auth::user()->role !== 'Contrôleur Financier') {
            return response()->json(['error' => 'Seul le Contrôleur Financier peut valider'], 403);
        }

        if ($piece->statut !== 'Brouillon') {
            return response()->json(['error' => 'Pièce déjà traitée'], 400);
        }

        $piece->update([
            'statut' => 'Validée',
            'validateur_id' => Auth::id(),
            'date_validation' => now()
        ]);

        return response()->json(['message' => 'Pièce validée avec succès']);
    }

    // === BONS DE COMMANDE & MARCHÉS (similaire) ===
    public function storeBonCommande(Request $request)
    {
        // Tous les services peuvent créer (selon GEDA : Services Techniques, RH, etc.)
        $data = $request->validate([
            'numero' => 'required|string|unique:bons_commande,numero',
            'objet' => 'required|string',
            'montant' => 'required|numeric',
            'fournisseur' => 'required|string',
        ]);

        $bon = BonCommande::create(array_merge($data, [
            'createur_id' => Auth::id(),
            'statut' => 'En attente de visa'
        ]));

        return response()->json($bon, 201);
    }

    public function viserBonCommande(BonCommande $bon, Request $request)
    {
        if (Auth::user()->role !== 'Contrôleur Financier') {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $data = $request->validate([
            'avis' => 'required|in:Favorable,Défavorable',
            'observations' => 'nullable|string'
        ]);

        $bon->update(array_merge($data, [
            'statut' => $data['avis'] === 'Favorable' ? 'Visé' : 'Rejeté',
            'validateur_id' => Auth::id(),
            'date_validation' => now()
        ]));

        return response()->json(['message' => 'Bon visé']);
    }
}
