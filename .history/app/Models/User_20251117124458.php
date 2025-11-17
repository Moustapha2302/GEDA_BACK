<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'service_code',
        'role'
    ];

    protected $hidden = ['password'];

    // Scopes utiles
    public function scopeS01($query)
    {
        return $query->where('service_code', 'S01');
    }

    // Helpers rapides
    // app/Models/User.php
    public function canAccessService(string $code): bool
    {
        if (in_array($this->role, ['maire', 'sg', 'directeur_cabinet'])) {
            return true;
        }
        return $this->service_code === $code;
    }

    public function isMaire(): bool
    {
        return $this->role === 'maire';
    }
    public function isSG(): bool
    {
        return $this->role === 'sg';
    }
    public function isChefS01(): bool
    {
        return $this->role === 'chef_s01';
    }
    public function isAgentS01(): bool
    {
        return $this->role === 'agent_s01';
    }
}
