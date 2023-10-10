<?php

namespace App\Service;

use App\Entity\Festival;
use App\Entity\Utilisateur;

class UtilisateurUtils {

    public function __construct() {
    }

    public function isBenevole(Utilisateur $u, Festival $f): bool {
        $benevoles = $f->getBenevoles();
        return $benevoles->filter(function ($benevole) use ($u) {
            return $benevole->getId() == $u->getId();
        })->count() > 0;
    }

    public function isResponsable(Utilisateur $u, Festival $f): bool {
        $responsables = $f->getResponsables();
        return $responsables->filter(function ($responsable) use ($u) {
            return $responsable->getId() == $u->getId();
        })->count() > 0;
    }

    public function isOrganisateur(Utilisateur $u, Festival $f): bool {
        $organisateur = $f->getOrganisateur();
        return $organisateur->getId() == $u->getId();
    }
}
