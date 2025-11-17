<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Acte;
use App\Http\Requests\StoreActeRequest;
use Illuminate\Http\Request;

class EtatCivilController extends Controller
{
    // app/Http/Controllers/Api/EtatCivilController.php
public function index()    { $this->authorize('viewAny', ActeEtatCivil::class); return ActeEtatCivil::with('createur')->paginate(20); }
public function show(ActeEtatCivil $acte) { $this->authorize('view', $acte); return $acte->load('createur','validePar'); }
public function store(Request $request)
{
    $this->authorize('create', ActeEtatCivil::class);
    $data = $request->validate([
        'numero_acte' => 'required|unique:actes_etat_civil',
        'type' => 'required|in:naissance,mariage,deces',
        'donnees' => 'required|array'
    ]);
    $acte = ActeEtatCivil::create($data + ['created_by' => auth()->id()]);
    return response()->json($acte, 201);
}
public function update(Request $request, ActeEtatCivil $acte)
{
    $this->authorize('update', $acte);
    $acte->update($request->only(['donnees', 'numero_acte']));
    return $acte;
}
public function valider(ActeEtatCivil $acte)
{
    $this->authorize('valider', $acte);
    $acte->update(['valide' => true, 'valide_par' => auth()->id(), 'date_validation' => now()]);
    return $acte;
}
public function certificat($type) { $this->authorize('genererCertificat', ActeEtatCivil::class); return response()->json(['message' => "Certificat $type généré"]); }
public function recherche(Request $request)
{
    $this->authorize('viewAny', ActeEtatCivil::class);
    $q = $request->q;
    return ActeEtatCivil::where('numero_acte', 'like', "%$q%")
        ->orWhereJsonContains('donnees->nom', 'like', "%$q%")
        ->orWhereJsonContains('donnees->prenom', 'like', "%$q%")
        ->get();
}
