<?php

// app/Models/PlanOccupationSol.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanOccupationSol extends Model
{
    protected $table = 'plans_occupation_sol';

    protected $fillable = [
        'nom',
        'fichier',
        'zone',
        'createur_id',
    ];

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createur_id');
    }
}
