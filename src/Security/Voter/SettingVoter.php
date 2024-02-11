<?php

namespace App\Security\Voter;

use App\Entity\Setting;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SettingVoter extends Voter
{
    public const EDIT = 'SETTING_EDIT';
    public const VIEW = 'SETTING_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Setting;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Setting $setting */
        $setting = $subject;

        return match ($attribute) {
            self::EDIT, self::VIEW => $setting->getUser() === $user,
            default => false,
        };

    }
}
