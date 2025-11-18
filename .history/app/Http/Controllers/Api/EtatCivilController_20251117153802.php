<?php
// app/Http/Controllers/Api/EtatCivilController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Acte;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ← authorize()
use Illuminate\Foundation\Bus\DispatchesJobs;              // ← middleware()
use Illuminate\Foundation\Validation\ValidatesRequests;    // ← validate()

class EtatCivilController extends Controller
{
    // LES 3 TRAITS OBLIGATOIRES POUR QUE TOUT FONCTIONNE
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // MIDDLEWARE SANCTUM SUR TOUTES LES MÉTHODES
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

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
        try {
            $this->authorize('create', Acte::class);

            $data = $request->validate([
                'numero_acte' => 'required|string|unique:actes,numero_acte',
                'type'        => 'required|in:naissance,mariage,deces',
                'donnees'     => 'required|array',
                // Validation détaillée des données de l'acte
                'donnees.prenom'         => 'required|string|max:255',
                'donnees.nom'            => 'required|string|max:255',
                'donnees.date_naissance' => 'required|date',
                'donnees.lieu_naissance' => 'required|string',
                'donnees.sexe'           => 'required|in:M,F',
                'donnees.nom_pere'       => 'required|string',
                'donnees.nom_mere'       => 'required|string',
            ]);

            $user = auth('sanctum')->user();
            if (!$user || $user->service_id != 1) {
                return response()->json(['message' => 'Accès refusé'], 403);
            }

            $acte = Acte::create([
                'numero_acte' => $data['numero_acte'],
                'type'        => $data['type'],
                'donnees'     => $data['donnees'],
                'created_by'  => $user->id,
                'service_id'  => 1,
            ]);

            return response()->json([
                'message' => 'Acte créé avec succès',
                'acte'    => $acte->load('createur')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Données invalides',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('Erreur création acte S01', [
                'user_id' => auth('sanctum')->id(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erreur serveur interne'
            ], 500);
        }
    }
    public function update(Request $request, Acte $acte)
    {
        $this->authorize('update', $acte);
        $acte->update($request->only(['donnees']));
        return $acte;
    }

    public function valider(Acte $acte)
    {
        $this->authorize('valider', $acte);

        $acte->update([
            'valide'          => true,
            'valide_par'      => auth('sanctum')->id(),
            'date_validation' => now()
        ]);

        return response()->json([
            'message' => 'Acte validé avec succès',
            'acte'    => $acte->fresh(['createur', 'validePar'])
        ]);
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
            ->orWhereJsonContains('donnees->nom', $q)
            ->orWhereJsonContains('donnees->prenom', $q)
            ->get();
    }
}
