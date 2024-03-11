<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 32)]
    private ?string $campaignId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCampaignId(): ?string
    {
        return $this->campaignId;
    }

    public function setCampaignId(string $campaignId): static
    {
        $this->campaignId = $campaignId;

        return $this;
    }
}
