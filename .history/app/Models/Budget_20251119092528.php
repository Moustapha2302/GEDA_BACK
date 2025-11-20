<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'budgets';

    protected $fillable = [
        'service',
        'annee',
        'budget_initial',
        'engage',
        'disponible',
    ];

    protected $casts = [
        'budget_initial' => 'decimal:2',
        'engage' => 'decimal:2',
        'disponible' => 'decimal:2',
    ];

    // Calculer automatiquement le disponible
    public function calculerDisponible()
    {
        $this->disponible = $this->budget_initial - $this->engage;
        $this->save();
    }
}
