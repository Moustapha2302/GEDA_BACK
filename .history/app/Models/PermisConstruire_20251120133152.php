<?php

// app/Models/PermisConstruire.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermisConstruire extends Model
{
    protected $table = 'permis_construire';

    protected $fillable = [
        'numero',
        'nom_demandeur',
        'prenom_demandeur',
        'adresse_terrain',
        'superficie',
        'type_projet',
        'statut',
        'createur_id',
        'viseur_id',
        'date_visa',
        'observations',
    ];

    protected $casts = [
        'date_visa' => 'datetime',
        'superficie' => 'decimal:2',
    ];

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createur_id');
    }

    public function viseur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viseur_id');
    }
}
