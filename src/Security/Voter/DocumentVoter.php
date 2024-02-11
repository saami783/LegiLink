<?php

namespace App\Security\Voter;

use App\Entity\Document;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumentVoter extends Voter
{
    public const VIEW = 'DOCUMENT_VIEW';
    public const DELETE = 'DOCUMENT_DELETE';
    public const DOWNLOAD = 'DOCUMENT_DOWNLOAD';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::DELETE, self::DOWNLOAD])
            && $subject instanceof Document;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Document $document */
        $document = $subject;

        return match ($attribute) {
            self::VIEW, self::DOWNLOAD, self::DELETE => $document->getUser() === $user,
            default => false,
        };

    }
}

