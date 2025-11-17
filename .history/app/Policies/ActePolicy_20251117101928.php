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
        return $user->isAgentS01() || $user->isChefS01();
    }

    public function update(User $user, Acte $acte): bool
    {
        if ($acte->valide) return false;
        return $user->isChefS01() || ($user->isAgentS01() && $acte->created_by === $user->id);
    }

    public function valider(User $user, Acte $acte): bool
    {
        return $user->isChefS01() || $user->isSG();
    }

    public function genererCertificat(User $user): bool
    {
        return $user->isAgentS01() || $user->isChefS01();
    }

    public function recherche(User $user): bool
    {
        return $user->canAccessS01();
    }
}
