<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonCommande extends Model
{
    protected $table = 'bons_commande';

    protected $fillable = [
        'numero',
        'objet',
        'montant',
        'fournisseur',
        'service_demandeur',
        'statut',
        'createur_id',
        'viseur_id',
        'date_visa',
        'avis',
        'observations',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_visa' => 'datetime',
    ];

    // Relations
    public function createur()
    {
        return $this->belongsTo(User::class, 'createur_id');
    }

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
}
