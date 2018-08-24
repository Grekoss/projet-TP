<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationRepository")
 */
class Participation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="participations")
     */
    private $participant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;


    public function __construct() {
        $this->createdAt= new \DateTime();
        $this->isReal = false;
        $this->hasRated = false;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="participations")
     */
    private $event;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isReal;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasRated;

    public function getId()
    {
        return $this->id;
    }

    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    public function setParticipant(?User $participant): self
    {
        $this->participant = $participant;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIsReal(): ?bool
    {
        return $this->isReal;
    }

    public function setIsReal(bool $isReal): self
    {
        $this->isReal = $isReal;

        return $this;
    }

    public function getHasRated(): ?bool
    {
        return $this->hasRated;
    }

    public function setHasRated(bool $hasRated): self
    {
        $this->hasRated = $hasRated;

        return $this;
    }
}
