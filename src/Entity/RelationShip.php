<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RelationShipRepository")
 */
class RelationShip
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="mainRelationShips")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userMain;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Link")
     * @ORM\JoinColumn(nullable=false)
     */
    private $link;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userConcernedRelationShips")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userConcerned;

    public function getId()
    {
        return $this->id;
    }

    public function getUserMain(): ?User
    {
        return $this->userMain;
    }

    public function setUserMain(?User $userMain): self
    {
        $this->userMain = $userMain;

        return $this;
    }

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(?Link $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getUserConcerned(): ?User
    {
        return $this->userConcerned;
    }

    public function setUserConcerned(?User $userConcerned): self
    {
        $this->userConcerned = $userConcerned;

        return $this;
    }
}
