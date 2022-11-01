<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 */
class Player
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"player"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"player"})
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
     * @Groups({"player"})
     */
    private $available;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"player"})
     */
    private $discord_tag;

    /**
     * @ORM\ManyToMany(targetEntity=GameOnPlatform::class, inversedBy="owners")
     * @ORM\JoinTable(name="player_owns_gameonplatform")
     */
    private $owned_games;

    /**
     * @ORM\ManyToMany(targetEntity=GameOnPlatform::class, inversedBy="players")
     * @ORM\JoinTable(name="player_wantstoplay_gameonplatform")
     */
    private $wants_to_play;

    public function __construct()
    {
        $this->owned_games = new ArrayCollection();
        $this->wants_to_play = new ArrayCollection();
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
    public function getOwnedGames(): Collection
    {
        return $this->owned_games;
    }

    public function addOwnedGame(GameOnPlatform $ownedGame): self
    {
        if (!$this->owned_games->contains($ownedGame)) {
            $this->owned_games[] = $ownedGame;
        }

        return $this;
    }

    public function removeOwnedGame(GameOnPlatform $ownedGame): self
    {
        $this->owned_games->removeElement($ownedGame);

        return $this;
    }

    /**
     * @return Collection<int, GameOnPlatform>
     */
    public function getWantsToPlay(): Collection
    {
        return $this->wants_to_play;
    }

    public function addWantsToPlay(GameOnPlatform $wantsToPlay): self
    {
        if (!$this->wants_to_play->contains($wantsToPlay)) {
            $this->wants_to_play[] = $wantsToPlay;
        }

        return $this;
    }

    public function removeWantsToPlay(GameOnPlatform $wantsToPlay): self
    {
        $this->wants_to_play->removeElement($wantsToPlay);

        return $this;
    }
}
