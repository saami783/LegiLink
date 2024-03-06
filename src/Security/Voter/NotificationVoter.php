<?php

namespace App\Security\Voter;

use App\Entity\Notification;
use App\Repository\NotificationUserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'NOTIFICATION_VIEW';
    private NotificationUserRepository $notificationUserRepository;

    public function __construct(NotificationUserRepository $notificationUserRepository) {
        $this->notificationUserRepository = $notificationUserRepository;
    }

    protected function supports(string $attribute, mixed $subject): bool {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Notification;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        $user = $token->getUser();
        if (!$user instanceof UserInterface || !$subject instanceof Notification) {
            return false;
        }

        $notificationUser = $this->notificationUserRepository->findOneBy([
            'user' => $user,
            'notification' => $subject,
        ]);

        if (!$notificationUser) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
        }

        return false;
    }



}
