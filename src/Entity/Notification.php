<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[Broadcast]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 0, nullable: false)]
    private ?string $message = null;

    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'notification', targetEntity: NotificationUser::class)]
    private Collection $notificationUsers;

    #[ORM\Column(length: 15)]
    private ?string $category = null;

    public function __construct()
    {
        $this->notificationUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function __toString() : string {
        return $this->message;
    }

    /**
     * @return Collection<int, User>
     */
    public function getNotificationUser(): Collection
    {
        return $this->notificationUsers;
    }

    public function addUser(NotificationUser $notificationUser): static
    {
        if (!$this->notificationUsers->contains($notificationUser)) {
            $this->notificationUsers->add($notificationUser);
        }

        return $this;
    }

    public function removeNotificationUser(NotificationUser $notificationUser): static
    {
        $this->notificationUsers->removeElement($notificationUser);

        return $this;
    }

    public function addNotificationUser(NotificationUser $notificationUser): self
    {
        if (!$this->notificationUsers->contains($notificationUser)) {
            $this->notificationUsers[] = $notificationUser;
            $notificationUser->setNotification($this);
        }

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

}
