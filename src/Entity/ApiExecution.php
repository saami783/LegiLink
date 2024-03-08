<?php

namespace App\Entity;

use App\Repository\ApiExecutionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiExecutionRepository::class)]
class ApiExecution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'apiExecutions')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $executedAt = null;

    #[ORM\Column]
    private ?int $execution = null;

    #[ORM\Column]
    private ?int $request = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getExecutedAt(): ?\DateTimeImmutable
    {
        return $this->executedAt;
    }

    public function setExecutedAt(\DateTimeImmutable $executedAt): static
    {
        $this->executedAt = $executedAt;

        return $this;
    }

    public function getExecution(): ?int
    {
        return $this->execution;
    }

    public function setExecution(int $execution): static
    {
        $this->execution = $execution;

        return $this;
    }

    public function getRequest(): ?int
    {
        return $this->request;
    }

    public function setRequest(int $request): static
    {
        $this->request = $request;

        return $this;
    }
}
