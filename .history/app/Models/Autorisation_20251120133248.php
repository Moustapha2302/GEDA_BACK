<?php

// app/Models/Autorisation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Autorisation extends Model
{
    protected $table = 'autorisations';

    protected $fillable = [
        'numero',
        'type',
        'nom_demandeur',
        'prenom_demandeur',
        'description',
        'statut',
        'createur_id',
    ];

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createur_id');
    }
}
