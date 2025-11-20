<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PieceComptable extends Model
{
    protected $table = 'pieces_comptables';

    use HasFactory;

    protected $fillable = [
        'type',
        'numero',
        'montant',
        'description',
        'service_beneficiaire',
        'statut',
        'validateur_id',
        'date_validation',
        // createur_id n'est PAS dans fillable → on le remplit automatiquement
    ];

    // AUTOMATIQUE : qui a créé cette pièce ?
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($piece) {
            if (Auth::check()) {
                $piece->createur_id = Auth::id();
            }
        });
    }

    // Relations (optionnelles mais très utiles)
    public function createur()
    {
        return $this->belongsTo(User::class, 'createur_id');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }
}
