<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
#[Broadcast]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    private ?int $dailyRequestLimit = 0;

    #[ORM\Column(nullable: false)]
    private ?int $requestAlertThreshold = 0;

    #[ORM\Column(nullable: false)]
    private ?bool $isAutoBlockRequests = false;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalRequestSent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDailyRequestLimit(): ?int
    {
        return $this->dailyRequestLimit;
    }

    public function setDailyRequestLimit(int $dailyRequestLimit): static
    {
        $this->dailyRequestLimit = $dailyRequestLimit;

        return $this;
    }

    public function getRequestAlertThreshold(): ?int
    {
        return $this->requestAlertThreshold;
    }

    public function setRequestAlertThreshold(int $requestAlertThreshold): static
    {
        $this->requestAlertThreshold = $requestAlertThreshold;

        return $this;
    }

    public function isIsAutoBlockRequests(): ?bool
    {
        return $this->isAutoBlockRequests;
    }

    public function setIsAutoBlockRequests(bool $isAutoBlockRequests): static
    {
        $this->isAutoBlockRequests = $isAutoBlockRequests;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTotalRequestSent(): ?int
    {
        return $this->totalRequestSent;
    }

    public function setTotalRequestSent(?int $totalRequestSent): static
    {
        $this->totalRequestSent = $totalRequestSent;

        return $this;
    }

}
