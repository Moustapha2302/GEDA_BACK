<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Acte;           // ← Ton modèle s'appelle Acte
use Illuminate\Http\Request;

class EtatCivilController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Acte::class);
        return Acte::with(['createur', 'validePar'])->paginate(20);
    }

    public function show(Acte $acte)
    {
        $this->authorize('view', $acte);
        return $acte->load(['createur', 'validePar']);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Acte::class);

        $data = $request->validate([
            'numero_acte' => 'required|string|unique:actes_etat_civil,numero_acte',
            'type'        => 'required|in:naissance,mariage,deces',
            'donnees'     => 'required|array'
        ]);

        $acte = Acte::create($data + ['created_by' => auth()->id()]);

        return response()->json($acte, 201);
    }

    public function update(Request $request, Acte $acte)
    {
        $this->authorize('update', $acte);
        $acte->update($request->only(['donnees', 'numero_acte']));
        return $acte;
    }

    public function valider(Acte $acte)
    {
        $this->authorize('valider', $acte);
        $acte->update([
            'valide' => true,
            'valide_par' => auth()->id(),
            'date_validation' => now()
        ]);
        return response()->json(['message' => 'Acte validé avec succès', 'acte' => $acte]);
    }

    public function certificat($type)
    {
        $this->authorize('genererCertificat', Acte::class);
        return response()->json(['message' => "Certificat $type généré avec succès"]);
    }

    public function recherche(Request $request)
    {
        $this->authorize('viewAny', Acte::class);
        $q = $request->query('q');

        return Acte::where('numero_acte', 'like', "%$q%")
            ->orWhereJsonContains('donnees->nom', 'like', "%$q%")
            ->orWhereJsonContains('donnees->prenom', 'like', "%$q%")
            ->get();
    }
}
