<?php

namespace App\Entity;

use App\Repository\MessageContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageContactRepository::class)]
class MessageContact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, nullable: false)]
    #[Assert\NotBlank(message: 'L\'adresse e-mail est requise.', groups: ['contact'])]
    #[Assert\Email(message: 'Veuillez entrer une adresse e-mail valide.')]
    private ?string $email = null;

    #[ORM\Column(length: '0')]
    #[Assert\NotBlank(message: "Votre message ne peut pas être vide.", groups: ["contact"])]
    #[Assert\Length(
        min: 10,
        max: 400,
        minMessage: "Votre message doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Votre message ne doit pas dépasser {{ limit }} caractères.",
        groups: ["contact"]
    )]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[ORM\Column]
    private ?bool $isBug = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function isIsBug(): ?bool
    {
        return $this->isBug;
    }

    public function setIsBug(bool $isBug): static
    {
        $this->isBug = $isBug;

        return $this;
    }
}
