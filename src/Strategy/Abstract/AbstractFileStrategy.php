<?php

namespace App\Strategy\Abstract;

use App\Entity\User;
use App\Strategy\Interface\FileStrategyInterface;

/**
 * Classe abstraite implémentant l'interface FileStrategyInterface.
 *
 * Cette classe sert de base pour toutes les stratégies concrètes de mise à jour de fichiers.
 */
class AbstractFileStrategy implements FileStrategyInterface
{

    /**
     * Exécute la stratégie de mise à jour du fichier.
     *
     * Cette méthode doit être implémentée par les classes concrètes.
     *
     * @param User $user L'utilisateur pour lequel la stratégie est exécutée
     */
    public function execute(User $user) : void { }

}