<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="tinyint")
     */
    private $see;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="messages")
     */
    private $send;

    /**
     * @ORM\ManyToOne(targetEntity=Conversation::class, inversedBy="messages")
     */
    private $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSee()
    {
        return $this->see;
    }

    public function setSee($see): self
    {
        $this->see = $see;

        return $this;
    }

    public function getSend(): ?Player
    {
        return $this->send;
    }

    public function setSend(?Player $send): self
    {
        $this->send = $send;

        return $this;
    }

    public function getContent(): ?Conversation
    {
        return $this->content;
    }

    public function setContent(?Conversation $content): self
    {
        $this->content = $content;

        return $this;
    }
}
