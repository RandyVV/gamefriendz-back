<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"discord_tag"}, message="There is already an account with this Discord tag")
 */
class Player implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"player"})
     * @Groups({"players"})
     * @Groups({"players_public"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"player"})
     * @Groups({"players"})
     * @Groups({"players_public"})
     * @Groups({"authenticate"})
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"authenticate"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"player"})
     * @Groups({"players"})
     * @Groups({"players_public"})
     */
    private $available = false;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"player"})
     * @Groups({"players"})
     * @Groups({"authenticate"})
     */
    private $discord_tag;

    /**
     * @ORM\ManyToMany(targetEntity=GameOnPlatform::class, inversedBy="owners")
     * @ORM\JoinTable(name="player_owns_gameonplatform")
     * @Groups({"player"})
     */
    private $owned_games;

    /**
     * @ORM\ManyToMany(targetEntity=GameOnPlatform::class, inversedBy="players")
     * @ORM\JoinTable(name="player_wantstoplay_gameonplatform")
     * @Groups({"player"})
     */
    private $wants_to_play;

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


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

    /**
     * @see PasswordAuthenticatedUserInterface
     */
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

    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
