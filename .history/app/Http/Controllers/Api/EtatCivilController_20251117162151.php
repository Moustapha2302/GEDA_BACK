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
        // Toutes les méthodes protégées par Sanctum + Policy
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $this->authorize('viewAny', Acte::class);

        $actes = Acte::with(['createur', 'validePar'])
            ->where('service_id', 1)
            ->latest()
            ->paginate(20);

        return response()->json($actes);
    }

    public function show(Acte $acte)
    {
        $this->authorize('view', $acte);
        return response()->json($acte->load(['createur', 'validePar']));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Acte::class);

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

        $donnees = $request->only([
            'prenom',
            'nom',
            'date_naissance',
            'lieu_naissance',
            'sexe',
            'nom_pere',
            'nom_mere',
            'profession_pere'
        ]);

        $acte = Acte::create([
            'numero_acte' => $validated['numero_acte'],
            'type'        => $validated['type'],
            'donnees'     => $donnees,
            'created_by'  => auth()->id(),
            'service_id'  => 1,
            'valide'      => false,
        ]);

        return response()->json([
            'message' => 'Acte créé avec succès',
            'acte'    => $acte->load('createur')
        ], 201);
    }

    public function update(Request $request, Acte $acte)
    {
        $this->authorize('update', $acte);

        $validated = $request->validate([
            'prenom'           => 'sometimes|required|string|max:255',
            'nom'              => 'sometimes|required|string|max:255',
            'date_naissance'   => 'sometimes|required|date',
            'lieu_naissance'   => 'sometimes|required|string|max:255',
            'sexe'             => 'sometimes|required|in:M,F',
            'nom_pere'         => 'sometimes|required|string|max:255',
            'nom_mere'         => 'sometimes|required|string|max:255',
            'profession_pere'  => 'nullable|string|max:255',
        ]);

        $donnees = array_merge($acte->donnees, $validated);
        $acte->update(['donnees' => $donnees]);

        return response()->json([
            'message' => 'Acte mis à jour avec succès',
            'acte'    => $acte->fresh()
        ]);
    }

    public function valider(Acte $acte)
    {
        $this->authorize('valider', $acte);

        $acte->update([
            'valide'          => true,
            'valide_par'      => auth()->id(),
            'date_validation' => now()
        ]);

        return response()->json([
            'message' => 'Acte validé avec succès',
            'acte'    => $acte->fresh(['createur', 'validePar'])
        ]);
    }

    public function recherche(Request $request)
    {
        $this->authorize('viewAny', Acte::class);

        $q = $request->query('q');
        if (!$q) {
            return response()->json(['message' => 'Paramètre requis'], 400);
        }

        $actes = Acte::where('service_id', 1)
            ->where(function ($query) use ($q) {
                $query->where('numero_acte', 'like', "%$q%")
                    ->orWhereJsonContains('donnees->prenom', $q)
                    ->orWhereJsonContains('donnees->nom', $q);
            })
            ->with('createur')
            ->get();

        return response()->json($actes);
    }

    public function genererCertificat($type)
    {
        $this->authorize('genererCertificat', Acte::class);
        return response()->json(['message' => "Certificat $type généré"]);
    }
}
