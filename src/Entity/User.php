<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Table(name="app_user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *  
 */
class User implements UserInterface, \Serializable 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * 
     * @Assert\NotBlank(message = "Veuillez saisir un pseudo")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100)
     * 
     * @Assert\NotBlank(message = "Veuillez saisir votre nom de famille")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=100)
     * 
     * @Assert\NotBlank(message = "Veuillez saisir votre prénom")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Assert\Image(
     *      allowLandscape = false,
     *      sizeNotDetectedMessage  = "L'image doit être orienté Portrait"
     * )
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank(message = "Saisir une ville")
     */
    private $city;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message = "Saisir une date de naissance")
     * @Assert\Date()
     * @Assert\Range(
     *      min = "-89 years",
     *      max = "-18 years",
     *      minMessage = "Désoler, vous ne trouverez pas votre bonheur. Nous vous invitons à nous contacter",
     *      maxMessage = "Désoler, les membres doivent être majeurs!"
     * )

     */
    private $birthDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=10)
     * 
     * @Assert\NotBlank(message = "Saisir votre code postal")
     */
    private $zipcode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Department", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $department;

    /**
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * 
     * @Assert\NotBlank (message = "Merci de saisir votre adresse Email")
     * @Assert\Email(
     *      message = "Ce n'est pas une adresse Email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     * 
     * @Assert\NotBlank (message = "Merci de saisir un mot de passe")
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="organize", orphanRemoval=true)
     */
    private $organizer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EventReporting", mappedBy="user", orphanRemoval=true)
     */
    private $eventReportings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserReporting", mappedBy="user", orphanRemoval=true)
     */
    private $userReportings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserReporting", mappedBy="accusedUser", orphanRemoval=true)
     */
    private $accused;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Genre")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Assert\NotBlank (message = "Selectionner votre genre")
     */
    private $genre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RelationShip", mappedBy="userMain")
     */
    private $mainRelationShips;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RelationShip", mappedBy="userConcerned")
     */
    private $userConcernedRelationShips;

    /**
     * @ORM\Column(type="integer")
     */
    private $evalCount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participation", mappedBy="participant")
     */
    private $participations;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isMailing;

    /**

     * @ORM\Column(type="datetime", nullable=true)
     */
    private $connectedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;
  
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="sendee", orphanRemoval=true)
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Following", mappedBy="user", cascade="remove")
     */
    private $followings;

   

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->organizer = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->eventReportings = new ArrayCollection();
        $this->userReportings = new ArrayCollection();
        $this->accused = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->rating = 5;
        $this->isActive = true;
        $this->mainRelationShips = new ArrayCollection();
        $this->userConcernedRelationShips = new ArrayCollection();
        $this->evalCount = 0;
        $this->participations = new ArrayCollection();
        $this->isMailing = true;
        $this->notifications = new ArrayCollection();
        $this->followings = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getBirthDate()
    {
        return $this->birthDate;
    }

    public function setBirthDate($birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(? Department $department) : self
    {
        $this->department = $department;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role)
    {
        $this->role = $role;

        return $this;
    }

    

    

    /**
     * @return Collection|Event[]
     */
    public function getOrganizer(): Collection
    {
        return $this->organizer;
    }

    public function addOrganizer(Event $organizer): self
    {
        if (!$this->organizer->contains($organizer)) {
            $this->organizer[] = $organizer;
            $organizer->setOrganize($this);
        }

        return $this;
    }

    public function removeOrganizer(Event $organizer): self
    {
        if ($this->organizer->contains($organizer)) {
            $this->organizer->removeElement($organizer);
            // set the owning side to null (unless already changed)
            if ($organizer->getOrganize() === $this) {
                $organizer->setOrganize(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EventReporting[]
     */
    public function getEventReportings(): Collection
    {
        return $this->eventReportings;
    }

    public function addEventReporting(EventReporting $eventReporting): self
    {
        if (!$this->eventReportings->contains($eventReporting)) {
            $this->eventReportings[] = $eventReporting;
            $eventReporting->setUser($this);
        }

        return $this;
    }

    public function removeEventReporting(EventReporting $eventReporting): self
    {
        if ($this->eventReportings->contains($eventReporting)) {
            $this->eventReportings->removeElement($eventReporting);
            // set the owning side to null (unless already changed)
            if ($eventReporting->getUser() === $this) {
                $eventReporting->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserReporting[]
     */
    public function getUserReportings(): Collection
    {
        return $this->userReportings;
    }

    public function addUserReporting(UserReporting $userReporting): self
    {
        if (!$this->userReportings->contains($userReporting)) {
            $this->userReportings[] = $userReporting;
            $userReporting->setUser($this);
        }

        return $this;
    }

    public function removeUserReporting(UserReporting $userReporting): self
    {
        if ($this->userReportings->contains($userReporting)) {
            $this->userReportings->removeElement($userReporting);
            // set the owning side to null (unless already changed)
            if ($userReporting->getUser() === $this) {
                $userReporting->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserReporting[]
     */
    public function getAccused(): Collection
    {
        return $this->accused;
    }

    public function addAccused(UserReporting $accused): self
    {
        if (!$this->accused->contains($accused)) {
            $this->accused[] = $accused;
            $accused->setAccusedUser($this);
        }

        return $this;
    }

    public function removeAccused(UserReporting $accused): self
    {
        if ($this->accused->contains($accused)) {
            $this->accused->removeElement($accused);
            // set the owning side to null (unless already changed)
            if ($accused->getAccusedUser() === $this) {
                $accused->setAccusedUser(null);
            }
        }

        return $this;
    }


    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection|RelationShip[]
     */
    public function getMainRelationShips(): Collection
    {
        return $this->mainRelationShips;
    }

    public function addMainRelationShip(RelationShip $mainRelationShip): self
    {
        if (!$this->mainRelationShips->contains($mainRelationShip)) {
            $this->mainRelationShips[] = $mainRelationShip;
            $mainRelationShip->setUser($this);
        }

        return $this;
    }

    public function removeMainRelationShip(RelationShip $mainRelationShip): self
    {
        if ($this->mainRelationShips->contains($mainRelationShip)) {
            $this->mainRelationShips->removeElement($mainRelationShip);
            // set the owning side to null (unless already changed)
            if ($mainRelationShip->getUser() === $this) {
                $mainRelationShip->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RelationShip[]
     */
    public function getUserConcernedRelationShips(): Collection
    {
        return $this->userConcernedRelationShips;
    }

    public function addUserConcernedRelationShip(RelationShip $userConcernedRelationShip): self
    {
        if (!$this->userConcernedRelationShips->contains($userConcernedRelationShip)) {
            $this->userConcernedRelationShips[] = $userConcernedRelationShip;
            $userConcernedRelationShip->setUserConcerned($this);
        }

        return $this;
    }

    public function removeUserConcernedRelationShip(RelationShip $userConcernedRelationShip): self
    {
        if ($this->userConcernedRelationShips->contains($userConcernedRelationShip)) {
            $this->userConcernedRelationShips->removeElement($userConcernedRelationShip);
            // set the owning side to null (unless already changed)
            if ($userConcernedRelationShip->getUserConcerned() === $this) {
                $userConcernedRelationShip->setUserConcerned(null);
            }
        }

        return $this;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getRoles()
    {
        return array($this->role->getName());
    }

    public function eraseCredentials()
    {
    }

     /** @see \Serializable::serialize() */
     public function serialize()
     {
         return serialize(array(
             $this->id,
             $this->username,
             $this->password,
             $this->isActive,
             //$this->email,
             // see section on salt below
             // $this->salt,
         ));
     }

     /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            //$this->email,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

   

    public function getEvalCount(): ?int
    {
        return $this->evalCount;
    }

    public function setEvalCount(int $evalCount): self
    {
        $this->evalCount = $evalCount;

        return $this;
    }

    /**
     * @return Collection|Participation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setParticipant($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
            // set the owning side to null (unless already changed)
            if ($participation->getParticipant() === $this) {
                $participation->setParticipant(null);
            }
        }

        return $this;
    }

    public function getIsMailing(): ?bool
    {
        return $this->isMailing;
    }

    public function setIsMailing(?bool $isMailing): self
    {
        $this->isMailing = $isMailing;

        return $this;
    }

    public function getConnectedAt(): ?\DateTimeInterface
    {
        return $this->connectedAt;
    }

    public function setConnectedAt(?\DateTimeInterface $connectedAt): self
    {
        $this->connectedAt = $connectedAt;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setSendee($this);
        }

        return $this;
    }
  
  public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getSendee() === $this) {
                $notification->setSendee(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAge()
    {
        $dateInterval = $this->birthDate->diff(new \DateTime());

        return $dateInterval->y;
    }

    /**
     * @return Collection|Following[]
     */
    public function getFollowings(): Collection
    {
        return $this->followings;
    }

    public function addFollowing(Following $following): self
    {
        if (!$this->followings->contains($following)) {
            $this->followings[] = $following;
            $following->setUser($this);
        }

        return $this;
    }

    public function removeFollowing(Following $following): self
    {
        if ($this->followings->contains($following)) {
            $this->followings->removeElement($following);
            // set the owning side to null (unless already changed)
            if ($following->getUser() === $this) {
                $following->setUser(null);
            }
        }

        return $this;
    }
}