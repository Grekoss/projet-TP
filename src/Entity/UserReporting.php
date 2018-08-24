<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserReportingRepository")
 */
class UserReporting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isManage;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userReportings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="accused")
     * @ORM\JoinColumn(nullable=false)
     */
    private $accusedUser;

    public function __construct()
    {
        $this->isManage = false;
        $this->dateAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->getDescription();
    }

    public function getId()
    {
        return $this->id;
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

    public function getIsManage(): ?bool
    {
        return $this->isManage;
    }

    public function setIsManage(bool $isManage): self
    {
        $this->isManage = $isManage;

        return $this;
    }

    public function getDateAt(): ?\DateTimeInterface
    {
        return $this->dateAt;
    }

    public function setDateAt(\DateTimeInterface $dateAt): self
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAccusedUser(): ?User
    {
        return $this->accusedUser;
    }

    public function setAccusedUser(?User $accusedUser): self
    {
        $this->accusedUser = $accusedUser;

        return $this;
    }
}
