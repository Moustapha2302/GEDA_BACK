<?php

namespace App\Policies;

use App\Models\Acte;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActePolicy
{
    use HandlesAuthorization;

    /**
     * Maire et Secrétaire Général ont tous les droits
     */
    public function before(User $user, $ability)
    {
        if (in_array($user->role, ['maire', 'sg'])) {
            return true;
        }
    }

    /**
     * Liste des actes - Tout le service S01
     */
    public function viewAny(User $user)
    {
        return $user->service_code === 'S01';
    }

    /**
     * Voir un acte spécifique
     */
    public function view(User $user, Acte $acte)
    {
        // Service S01 ou créateur de l'acte
        return $user->service_code === 'S01' || $user->id === $acte->created_by;
    }

    /**
     * Créer un acte → Agent S01 + Chef S01
     */
    public function create(User $user)
    {
        return $user->service_code === 'S01'
            && in_array($user->role, ['agent_s01', 'chef_s01']);
    }

    /**
     * Modifier → uniquement le créateur + acte non validé
     */
    public function update(User $user, Acte $acte)
    {
        return $user->id === $acte->created_by && !$acte->valide;
    }

    /**
     * Valider → UNIQUEMENT le Chef du service État Civil
     */
    public function valider(User $user, Acte $acte)
    {
        return $user->role === 'chef_s01'
            && $user->service_code === 'S01'
            && !$acte->valide;
    }

    /**
     * Générer certificat - Tout le service S01
     */
    public function genererCertificat(User $user)
    {
        return $user->service_code === 'S01';
    }

    /**
     * Supprimer un acte - Chef S01 seulement si non validé
     */
    public function delete(User $user, Acte $acte)
    {
        return $user->role === 'chef_s01'
            && $user->service_code === 'S01'
            && !$acte->valide;
    }
}
