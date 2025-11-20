<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarchePublic extends Model
{
    protected $table = 'marches_publics';

    protected $fillable = [
        'numero',
        'objet',
        'montant',
        'attributaire',
        'date_debut',
        'date_fin',
        'statut',
        'viseur_id',
        'date_visa',
        'avis',
        'observations',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_visa' => 'datetime',
    ];

    // Relations
    public function viseur()
    {
        return $this->belongsTo(User::class, 'viseur_id');
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'En attente');
    }

    public function scopeVise($query)
    {
        return $query->where('statut', 'Visé');
    }

    public function scopeRejete($query)
    {
        return $query->where('statut', 'Rejeté');
    }

    public function scopeEnCours($query)
    {
        return $query->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now());
    }
}
