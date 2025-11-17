<?php

namespace App\Policies;

use App\Models\Acte;
use App\Models\User;

class ActePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessS01();
    }

    public function view(User $user, Acte $acte): bool
    {
        return $user->canAccessS01();
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['agent_s01', 'chef_s01']);
    }

    public function update(User $user, Acte $acte): bool
    {
        return !$acte->valide && (
            $user->role === 'chef_s01' ||
            ($user->role === 'agent_s01' && $acte->created_by === $user->id)
        );
    }

    public function valider(User $user, Acte $acte): bool
    {
        return in_array($user->role, ['chef_s01', 'sg', 'maire']);
    }

    public function genererCertificat(User $user): bool
    {
        return in_array($user->role, ['agent_s01', 'chef_s01']);
    }
}
