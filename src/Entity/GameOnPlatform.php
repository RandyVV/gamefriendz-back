<?php

namespace App\Entity;

use App\Repository\GameOnPlatformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameOnPlatformRepository::class)
 */
class GameOnPlatform
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $release_date;

    /**
     * @ORM\ManyToMany(targetEntity=Player::class, mappedBy="owned_games")
     */
    private $owners;

    /**
     * @ORM\ManyToMany(targetEntity=Player::class, mappedBy="wants_to_play")
     */
    private $players;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->release_date;
    }

    public function setReleaseDate(\DateTimeInterface $release_date): self
    {
        $this->release_date = $release_date;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->addWantsToPlay($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->removeElement($player)) {
            $player->removeWantsToPlay($this);
        }

        return $this;
    }
    
    /**
     * @return Collection<int, Player>
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(Player $owner): self
    {
        if (!$this->owners->contains($owner)) {
            $this->owners[] = $owner;
            $owner->addOwnedGame($this);
        }

        return $this;
    }

    public function removeOwner(Player $owner): self
    {
        if ($this->owners->removeElement($owner)) {
            $owner->removeOwnedGame($this);
        }

        return $this;
    }
    
}