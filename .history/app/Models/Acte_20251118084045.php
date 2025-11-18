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
        'created_by',
        'valide',
        'valide_par',
        'date_validation'
    ];

    protected $casts = [
        'donnees' => 'array',
        'valide' => 'boolean',
        'date_validation' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    // Scopes
    public function scopeValide($query)
    {
        return $query->where('valide', true);
    }

    public function scopeNonValide($query)
    {
        return $query->where('valide', false);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accesseurs
    public function getNomCompletAttribute()
    {
        return ($this->donnees['prenom'] ?? '') . ' ' . ($this->donnees['nom'] ?? '');
    }
}
