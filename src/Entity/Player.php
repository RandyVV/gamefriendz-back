<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 */
class Player
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $available;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $discord_tag;

    /**
     * @ORM\ManyToMany(targetEntity=GameOnPlatform::class, inversedBy="players")
     */
    private $gameonplatform;

    public function __construct()
    {
        $this->gameonplatform = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getDiscordTag(): ?string
    {
        return $this->discord_tag;
    }

    public function setDiscordTag(string $discord_tag): self
    {
        $this->discord_tag = $discord_tag;

        return $this;
    }

    /**
     * @return Collection<int, GameOnPlatform>
     */
    public function getGameonplatform(): Collection
    {
        return $this->gameonplatform;
    }

    public function addGameonplatform(GameOnPlatform $gameonplatform): self
    {
        if (!$this->gameonplatform->contains($gameonplatform)) {
            $this->gameonplatform[] = $gameonplatform;
        }

        return $this;
    }

    public function removeGameonplatform(GameOnPlatform $gameonplatform): self
    {
        $this->gameonplatform->removeElement($gameonplatform);

        return $this;
    }
}
