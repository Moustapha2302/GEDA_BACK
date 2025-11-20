<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFinancier extends Model
{
    use HasFactory;

    protected $table = 'documents_financiers';

    protected $fillable = [
        'numero_document',
        'type',
        'donnees',
        'created_by',
        'valide',
        'valide_par',
        'date_validation',
    ];

    protected $casts = [
        'donnees' => 'array',
        'valide' => 'boolean',
        'date_validation' => 'datetime',
    ];

    // Relation: Créateur du document
    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation: Validateur du document
    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    // Scope: Documents valides uniquement
    public function scopeValides($query)
    {
        return $query->where('valide', true);
    }

    // Scope: Documents en attente de validation
    public function scopeEnAttente($query)
    {
        return $query->where('valide', false);
    }

    // Scope: Par type
    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope: Par exercice
    public function scopeParExercice($query, $exercice)
    {
        return $query->whereRaw("JSON_EXTRACT(donnees, '$.exercice') = ?", [$exercice]);
    }
}
