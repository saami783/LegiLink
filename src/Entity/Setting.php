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

}
