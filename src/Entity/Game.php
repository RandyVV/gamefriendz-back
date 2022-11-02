<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"games"})
     * @Groups({"game"})
     * @Groups({"player"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"games"})
     * @Groups({"game"})
     * @Groups({"player"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"game"})
     * @Groups({"player"})
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"game"})
     * @Groups({"player"})
     */
    private $has_multiplayer_mode;

    /**
     * @ORM\OneToMany(targetEntity=GameOnPlatform::class, mappedBy="game", orphanRemoval=true)
     * @Groups({"game"})
     */
    private $releases;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"games"})
     * @Groups({"game"})
     * @Groups({"player"})
     */
    private $picture;

    public function __construct()
    {
        $this->releases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isHasMultiplayerMode(): ?bool
    {
        return $this->has_multiplayer_mode;
    }

    public function setHasMultiplayerMode(bool $has_multiplayer_mode): self
    {
        $this->has_multiplayer_mode = $has_multiplayer_mode;

        return $this;
    }

    /**
     * @return Collection<int, GameOnPlatform>
     */
    public function getReleases(): Collection
    {
        return $this->releases;
    }

    public function addRelease(GameOnPlatform $release): self
    {
        if (!$this->releases->contains($release)) {
            $this->releases[] = $release;
            $release->setGame($this);
        }

        return $this;
    }

    public function removeRelease(GameOnPlatform $release): self
    {
        if ($this->releases->removeElement($release)) {
            // set the owning side to null (unless already changed)
            if ($release->getGame() === $this) {
                $release->setGame(null);
            }
        }

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}
