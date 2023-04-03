<?php

namespace App\Entity\BackOffice;

use App\Repository\PlatformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlatformRepository::class)
 */
class Platform
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"platforms"})
     * @Groups({"game"})
     * @Groups({"player"})
     * @Groups({"games"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"platforms"})
     * @Groups({"game"})
     * @Groups({"player"})
     * @Groups({"games"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=GameOnPlatform::class, mappedBy="platform", orphanRemoval=true)
     */
    private $supportedGames;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"platforms"})
     * @Groups({"game"})
     * @Groups({"player"})
     * @Groups({"games"})
     */
    private $slug;

    public function __construct()
    {
        $this->supportedGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, GameOnPlatform>
     */
    public function getSupportedGames(): Collection
    {
        return $this->supportedGames;
    }

    public function addSupportedGame(GameOnPlatform $supportedGame): self
    {
        if (!$this->supportedGames->contains($supportedGame)) {
            $this->supportedGames[] = $supportedGame;
            $supportedGame->setPlatform($this);
        }

        return $this;
    }

    public function removeSupportedGame(GameOnPlatform $supportedGame): self
    {
        if ($this->supportedGames->removeElement($supportedGame)) {
            // set the owning side to null (unless already changed)
            if ($supportedGame->getPlatform() === $this) {
                $supportedGame->setPlatform(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
