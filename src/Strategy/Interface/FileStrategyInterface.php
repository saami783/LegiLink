<?php

namespace App\Strategy\Interface;

use App\Entity\User;

/**
 * Interface définissant la structure des stratégies de mise à jour de fichiers.
 */
interface FileStrategyInterface
{
    /**
     * Exécute la stratégie de mise à jour du fichier.
     *
     * @param User $user L'utilisateur pour lequel la stratégie est exécutée
     */
    public function execute(User $user) : void ;

}