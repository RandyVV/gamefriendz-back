<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 */
class Conversation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Player::class, inversedBy="conversations")
     */
    private $participate;

    public function __construct()
    {
        $this->participate = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getParticipate(): Collection
    {
        return $this->participate;
    }

    public function addParticipate(Player $participate): self
    {
        if (!$this->participate->contains($participate)) {
            $this->participate[] = $participate;
        }

        return $this;
    }

    public function removeParticipate(Player $participate): self
    {
        $this->participate->removeElement($participate);

        return $this;
    }
}
