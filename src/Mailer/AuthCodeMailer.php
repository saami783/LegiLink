<?php

namespace App\Mailer;

use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @final
 */
class AuthCodeMailer implements AuthCodeMailerInterface
{
    private Address|string|null $senderAddress = null;
    private TwigEnvironment $environment;

    public function __construct(
        private readonly MailerInterface $mailer,
        TwigEnvironment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $authCode = $user->getEmailAuthCode();

        $message = (new Email())
            ->from('no-reply-legilink@legilink.com')
            ->to($user->getEmailAuthRecipient())
            ->subject("Votre code d'authentification")
            ->html($this->environment->render('mails/auth_code.html.twig', [
                'authCode' => $authCode,
            ]));


        if (null !== $this->senderAddress) {
            $message->from($this->senderAddress);
        }

        $this->mailer->send($message);
    }
}