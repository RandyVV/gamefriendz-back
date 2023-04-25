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

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="content")
     */
    private $messages;

    public function __construct()
    {
        $this->participate = new ArrayCollection();
        $this->messages = new ArrayCollection();
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

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setContent($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getContent() === $this) {
                $message->setContent(null);
            }
        }

        return $this;
    }
}
