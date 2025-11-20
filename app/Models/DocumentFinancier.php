<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentFinancier extends Model
{
    use HasFactory;

    protected $table = 'documents_financiers';

    /**
     * Les attributs qui sont assignables en masse.
     */
    protected $fillable = [
        'numero_document',
        'type',
        'donnees',           // JSON : objet, montant, bénéficiaire, date_emission, exercice, etc.
        'created_by',
        'valide',
        'valide_par',
        'date_validation',
        'visa_controleur',
        'date_visa',
    ];

    /**
     * Casts automatiques
     */
    protected $casts = [
        'donnees'         => 'array',           // très important pour accéder facilement aux données JSON
        'valide'          => 'boolean',
        'visa_controleur' => 'boolean',
        'date_validation' => 'datetime',
        'date_visa'       => 'datetime',
    ];

    /**
     * Types de documents financiers autorisés (conforme au cahier des charges)
     */
    const TYPES = [
        'facture',
        'bon_commande',
        'mandat',
        'ordre_paiement',
        'etat_depense',
        'budget',
        'marche_public',
        'piece_comptable',
    ];

    // ===================================================================
    // RELATIONS
    // ===================================================================

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function visePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visa_controleur'); // réutilise la même table users
    }

    // ===================================================================
    // SCOPES UTILES
    // ===================================================================

    public function scopeValides($query)
    {
        return $query->where('valide', true);
    }

    public function scopeEnAttenteValidation($query)
    {
        return $query->where('valide', false);
    }

    public function scopeAvecVisa($query)
    {
        return $query->where('visa_controleur', true);
    }

    public function scopeParType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeParExercice($query, int $exercice)
    {
        return $query->whereRaw("JSON_EXTRACT(donnees, '$.exercice') = ?", [$exercice]);
    }

    public function scopeParBeneficiaire($query, string $beneficiaire)
    {
        return $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(donnees, '$.beneficiaire'))) LIKE ?", ['%' . strtolower($beneficiaire) . '%']);
    }

    // ===================================================================
    // ACCESSORS (accès pratiques aux données JSON)
    // ===================================================================

    public function getObjetAttribute()
    {
        return $this->donnees['objet'] ?? null;
    }

    public function getMontantAttribute()
    {
        return $this->donnees['montant'] ?? 0;
    }

    public function getBeneficiaireAttribute()
    {
        return $this->donnees['beneficiaire'] ?? null;
    }

    public function getDateEmissionAttribute()
    {
        return $this->donnees['date_emission'] ?? null;
    }

    public function getExerciceAttribute()
    {
        return $this->donnees['exercice'] ?? null;
    }

    public function getLigneBudgetaireAttribute()
    {
        return $this->donnees['ligne_budgetaire'] ?? null;
    }

    // ===================================================================
    // MÉTHODES MÉTIER
    // ===================================================================

    public function estValide(): bool
    {
        return $this->valide === true;
    }

    public function aVisaControleur(): bool
    {
        return $this->visa_controleur === true;
    }

    public function peutEtreModifiePar(User $user): bool
    {
        return $this->created_by === $user->id && !$this->estValide();
    }

    public function peutEtreValidePar(User $user): bool
    {
        return in_array($user->role, ['chef_s02', 'secretaire_general', 'maire']);
    }

    public function peutRecevoirVisa(User $user): bool
    {
        return $user->role === 'controleur_financier';
    }
}
