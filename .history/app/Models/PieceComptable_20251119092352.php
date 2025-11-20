<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PieceComptable extends Model
{
    protected $table = 'pieces_comptables';

    protected $fillable = [
        'type',
        'numero',
        'montant',
        'description',
        'service_beneficiaire',
        'statut',
        'createur_id',
        'validateur_id',
        'date_validation',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_validation' => 'datetime',
    ];

    // Relations
    public function createur()
    {
        return $this->belongsTo(User::class, 'createur_id');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    // Scopes
    public function scopeBrouillon($query)
    {
        return $query->where('statut', 'Brouillon');
    }

    public function scopeValidee($query)
    {
        return $query->where('statut', 'Validée');
    }
}
