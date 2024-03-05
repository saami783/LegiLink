<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'NOTIFICATION_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Notification;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // Si l'utilisateur est anonyme, ne pas autoriser l'accès
        if (!$user instanceof UserInterface) {
            return false;
        }

        // On s'assure que $subject est bien une instance de Notification
        if (!$subject instanceof \App\Entity\Notification) {
            throw new \LogicException('Le voteur ne supporte que les instances de Notification.');
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $user);
        }

        throw new \LogicException('Cet attribut n\'est pas supporté !');
    }

    private function canView(\App\Entity\Notification $notification, UserInterface $user): bool
    {
        // Autoriser la visualisation si l'utilisateur est le propriétaire de la notification
        return $user === $notification->getUser();
    }


}
