<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'service_code',   // S01, S02, S03...
        'role'            // maire, sg, chef_s01, agent_s01, chef_s02, etc.
    ];

    protected $hidden = ['password'];

    // ================================================================
    // RÈGLES OFFICIELLES DE ZIGUINCHOR – VALIDÉES PAR LE MAIRE
    // ================================================================

    public function isMaire(): bool
    {
        return $this->role === 'maire';
    }

    public function isSG(): bool
    {
        return $this->role === 'sg';
    }

    // SEULS CES DEUX RÔLES PEUVENT VALIDER LES ACTES D'ÉTAT CIVIL
    public function peutValiderActesEtatCivil(): bool
    {
        return $this->role === 'chef_s01' || $this->role === 'sg';
    }

    // Compatibilité avec l'ancien code
    public function isChefS01(): bool
    {
        return $this->role === 'chef_s01';
    }

    public function isAgentS01(): bool
    {
        return $this->role === 'agent_s01';
    }

    // Accès transversal (lecture partout)
    public function hasAccesTransversal(): bool
    {
        return in_array($this->role, ['maire', 'sg', 'directeur_cabinet']);
    }

    public function canAccessService(string $code): bool
    {
        if ($this->hasAccesTransversal()) {
            return true;
        }
        return $this->service_code === $code;
    }
}
