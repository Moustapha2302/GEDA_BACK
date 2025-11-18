<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acte extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_acte',
        'type',
        'donnees',
        'valide',
        'created_by',
        'valide_par',
        'date_validation',
        'service_id' // ← À AJOUTER
    ];

    protected $casts = [
        'donnees' => 'array',
        'valide' => 'boolean',
        'date_validation' => 'datetime'
    ];

    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
