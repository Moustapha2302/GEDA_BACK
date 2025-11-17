<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActeEtatCivil;                    // ← CORRIGÉ ICI
use Illuminate\Http\Request;

class EtatCivilController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ActeEtatCivil::class);
        return ActeEtatCivil::with(['createur', 'validePar'])->paginate(20);
    }

    public function show(ActeEtatCivil $acte)
    {
        $this->authorize('view', $acte);
        return $acte->load(['createur', 'validePar']);
    }

    public function store(Request $request)
    {
        $this->authorize('create', ActeEtatCivil::class);

        $validated = $request->validate([
            'numero_acte' => 'required|string|unique:actes_etat_civil,numero_acte',
            'type'        => 'required|in:naissance,mariage,deces',
            'donnees'     => 'required|array'
        ]);

        $acte = ActeEtatCivil::create([
            'numero_acte' => $validated['numero_acte'],
            'type'        => $validated['type'],
            'donnees'     => $validated['donnees'],
            'created_by'  => auth()->id(),
        ]);

        return response()->json($acte, 201);
    }

    public function update(Request $request, ActeEtatCivil $acte)
    {
        $this->authorize('update', $acte);
        $acte->update($request->only(['donnees', 'numero_acte']));
        return response()->json($acte);
    }

    public function valider(ActeEtatCivil $acte)
    {
        $this->authorize('valider', $acte);
        $acte->update([
            'valide'          => true,
            'valide_par'      => auth()->id(),
            'date_validation' => now()
        ]);
        return response()->json(['message' => 'Acte validé avec succès', 'acte' => $acte]);
    }

    public function certificat($type)
    {
        $this->authorize('genererCertificat', ActeEtatCivil::class);
        return response()->json(['message' => "Certificat de {$type} généré avec succès"]);
    }

    public function recherche(Request $request)
    {
        $this->authorize('viewAny', ActeEtatCivil::class);
        $q = $request->query('q');

        $actes = ActeEtatCivil::where('numero_acte', 'like', "%{$q}%")
            ->orWhereJsonContains('donnees->nom', 'like', "%{$q}%")
            ->orWhereJsonContains('donnees->prenom', 'like', "%{$q}%")
            ->get();

        return response()->json($actes);
    }
}
