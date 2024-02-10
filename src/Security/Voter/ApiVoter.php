<?php

namespace App\Security\Voter;

use App\Entity\Api;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiVoter extends Voter
{
    public const EDIT = 'API_EDIT';
    public const VIEW = 'API_VIEW';
    public const DELETE = 'API_DELETE';
    public const NEW = 'API_NEW';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::NEW])
            && $subject instanceof Api;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Api $api */
        $api = $subject;

        return match ($attribute) {
            self::EDIT, self::VIEW, self::DELETE => $api->getUser() === $user,
            self::NEW => true,
            default => false,
        };
    }

}
