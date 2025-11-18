<?php

namespace App\Policies;

use App\Models\Acte;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActePolicy
{
    use HandlesAuthorization;

    // Maire et Secrétaire Général voient TOUT
    public function before(User $user)
    {
        if (in_array($user->type_acteur, ['maire', 'sg'])) {
            return true;
        }
    }

    // Liste des actes
    public function viewAny(User $user)
    {
        return $user->service_id == 1; // Tout le service État Civil (S01)
    }

    // Voir un acte spécifique
    public function view(User $user, Acte $acte)
    {
        return $user->service_id == 1 || $user->id === $acte->created_by;
    }

    // Créer un acte → Agent S01 + Chef S01
    public function create(User $user)
    {
        return $user->service_id == 1; // Tout le monde dans S01 peut créer
    }

    // Modifier → uniquement le créateur + acte non validé
    public function update(User $user, Acte $acte)
    {
        return $user->id === $acte->created_by && !$acte->valide;
    }

    // Valider → UNIQUEMENT le Chef du service État Civil
    public function valider(User $user, Acte $acte)
    {
        return $user->type_acteur === 'chef_service'
            && $user->service_id == 1
            && !$acte->valide;
    }

    // Générer certificat
    public function genererCertificat(User $user)
    {
        return $user->service_id == 1;
    }
}
