<?php
// app/Policies/ActePolicy.php

namespace App\Policies;

use App\Models\Acte;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActePolicy
{
    use HandlesAuthorization;

    // Maire et SG voient tout
    public function before(User $user)
    {
        if (in_array($user->type_acteur, ['maire', 'sg'])) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->service_id == 1 || $user->type_acteur === 'chef_service' && $user->service_id == 1;
    }

    public function view(User $user, Acte $acte)
    {
        return $user->service_id == 1 || $user->id === $acte->created_by;
    }

    public function create(User $user)
    {
        return $user->service_id == 1; // S01 uniquement
    }

    public function update(User $user, Acte $acte)
    {
        return $user->id === $acte->created_by && !$acte->valide;
    }

    public function valider(User $user, Acte $acte)
    {
        return $user->type_acteur === 'chef_service'
            && $user->service_id == 1
            && $acte->service_id == 1
            && !$acte->valide;
    }

    public function genererCertificat(User $user)
    {
        return $user->service_id == 1; // S01 uniquement
    }
}
