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
        'nom',           // ✅ Ajouté
        'prenom',        // ✅ Ajouté
        'email',
        'password',
        'service_code',
        'service',       // ✅ Ajouté pour compatibilité avec LoginController
        'role'
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
        // Accepte les deux formes réelles présentes dans ta base
        if ($this->role === 'sg') {
            return true;
        }

        // Accepte chef_s01 OU chef_service (le rôle que tu as vraiment)
        if ($this->service_code === 'S01' && in_array($this->role, ['chef_s01', 'chef_service'])) {
            return true;
        }

        return false;
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

    // ================================================================
    // MÉTHODES POUR LE SERVICE FINANCE (S02)
    // À ajouter dans App\Models\User.php
    // ================================================================

    public function isControleurFinancier(): bool
    {
        return in_array($this->role, ['controleur_financier', 'Contrôleur Financier']);
    }

    public function isChefS02(): bool
    {
        $serviceCode = $this->service_code ?? $this->service;
        return in_array($this->role, ['chef_s02', 'Chef S02', 'chef_service'])
            && $serviceCode === 'S02';
    }

    public function isAgentS02(): bool
    {
        $serviceCode = $this->service_code ?? $this->service;
        return in_array($this->role, ['agent_s02', 'Agent S02'])
            && $serviceCode === 'S02';
    }

    public function peutValiderDocumentsFinanciers(): bool
    {
        return in_array($this->role, [
            'chef_s02',
            'Chef S02',
            'secretaire_general',
            'maire',
            'Maire',
            'sg',
            'SG'
        ]) || ($this->isChefS02());
    }

    public function peutConsulterStatistiquesFinancieres(): bool
    {
        return in_array($this->role, [
            'controleur_financier',
            'Contrôleur Financier',
            'sg',
            'SG',
            'maire',
            'Maire',
            'Chef S02',
            'chef_s02'
        ]) || $this->isChefS02();
    }

    public function peutAccederFinance(): bool
    {
        // Utiliser service_code ou service
        $serviceCode = $this->service_code ?? $this->service;

        return $serviceCode === 'S02'
            || in_array($this->role, [
                'Contrôleur Financier',
                'controleur_financier',
                'Chef S02',
                'chef_s02',
                'Agent S02',
                'agent_s02',
                'maire',
                'Maire',
                'sg',
                'SG'
            ]);
    }
}
