<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="app_event")
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank(message = "Veuillez saisir un Titre pour votre évènement")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=10)
     * 
     * @Assert\NotBlank(message = "Saisir le code postal du lieu")
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank(message = "Saisir une ville")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Assert\Image()
     */
    private $photo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="date")
     * 
     * @Assert\NotBlank(message = "Saisir une date")
     * @Assert\Date()
     * @Assert\Range(
     *      min = "-1 day",
     *      max = "2 years",
     *      minMessage = "Vous ne pouvez pas planifier un évènement antérieur à la date d'aujourd'hui",
     *      maxMessage = "Vous ne pouvez pas plannifer un évènement avec deux ans d'avance!"
     *      
     * )
     */
    private $dateAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text")
     * 
     * @Assert\NotBlank ( message = "Veuillez saisir la description de votre sortie")
     * @Assert\Length(
     *      max = 2000,
     *      maxMessage = "Vous ne pouvez pas saisir plus de {{ limit }} caractères.")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Department", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $department;

   

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="organizer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organize;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="events")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="event", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EventReporting", mappedBy="event", orphanRemoval=true)
     */
    private $eventReportings;

    /**
     * @ORM\Column(type="integer")
     * 
     * @Assert\NotBlank (message = "Saisir le nombre de participants")
     * @Assert\Range(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Vous devez être au moins deux participants",
     *      maxMessage = "Vous êtes limité à 20 participants"
     * )
     */
    private $participantsLimit;

    /**
     * @ORM\Column(type="time")
     * 
     */
    private $timeAt;

    

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participation", mappedBy="event", cascade="remove")
     */
    private $participations;

   

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Following", mappedBy="event", cascade="remove")
     */
    private $followings;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Visibility", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $visibility;

    /**
     * @ORM\Column(type="datetime")
     *  @Assert\DateTime()
     *  @Assert\Range(
     *      min = "now",
     * )
     */
    private $joinTimeLimit;

    /**
     * @ORM\Column(type="integer")
     *  @Assert\Range(
     *      min = 18,
     *      max = 85,
     * )
     */
    private $minAge;

    /**
     * @ORM\Column(type="integer")
     *  @Assert\Range(
     *      min = 18,
     *      max = 85,
     * )
     */
    private $maxAge;

   

   

    

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->eventReportings = new ArrayCollection();
        $this->isActive = true;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->trueParticipants = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->visibilities = new ArrayCollection();
        $this->followings = new ArrayCollection();
        $this->minAge = 18;
        $this->maxAge = 85;
        
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getId()
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

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo)
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

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

    public function getDateAt()
    {
        return $this->dateAt;
    }

    public function setDateAt($dateAt): self
    {
        $this->dateAt = $dateAt;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    

    public function getOrganize(): ?User
    {
        return $this->organize;
    }

    public function setOrganize(?User $organize): self
    {
        $this->organize = $organize;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
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
            $comment->setEvent($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getEvent() === $this) {
                $comment->setEvent(null);
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
            $eventReporting->setEvent($this);
        }

        return $this;
    }

    public function removeEventReporting(EventReporting $eventReporting): self
    {
        if ($this->eventReportings->contains($eventReporting)) {
            $this->eventReportings->removeElement($eventReporting);
            // set the owning side to null (unless already changed)
            if ($eventReporting->getEvent() === $this) {
                $eventReporting->setEvent(null);
            }
        }

        return $this;
    }

    public function getParticipantsLimit(): ?int
    {
        return $this->participantsLimit;
    }

    public function setParticipantsLimit(int $participantsLimit): self
    {
        $this->participantsLimit = $participantsLimit;

        return $this;
    }

    public function getTimeAt(): ?\DateTimeInterface
    {
        return $this->timeAt;
    }

    public function setTimeAt(\DateTimeInterface $timeAt): self
    {
        $this->timeAt = $timeAt;

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
            $participation->setEvent($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
            // set the owning side to null (unless already changed)
            if ($participation->getEvent() === $this) {
                $participation->setEvent(null);
            }
        }

        return $this;
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
            $following->setEvent($this);
        }

        return $this;
    }

    public function removeFollowing(Following $following): self
    {
        if ($this->followings->contains($following)) {
            $this->followings->removeElement($following);
            // set the owning side to null (unless already changed)
            if ($following->getEvent() === $this) {
                $following->setEvent(null);
            }
        }

        return $this;
    }

    public function isFollowedBy(User $user){
        foreach($this->getFollowings() as $following) 
        {
            if($following->getUser() == $user) {
                return true;
            }
        }
    }

    public function getVisibility(): ?Visibility
    {
        return $this->visibility;
    }

    public function setVisibility(?Visibility $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getJoinTimeLimit(): ?\DateTimeInterface
    {
        return $this->joinTimeLimit;
    }

    public function setJoinTimeLimit(\DateTimeInterface $joinTimeLimit): self
    {
        $this->joinTimeLimit = $joinTimeLimit;

        return $this;
    }

    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    public function setMinAge(?int $minAge): self
    {
        $this->minAge = $minAge;

        return $this;
    }

    public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    public function setMaxAge(?int $maxAge): self
    {
        $this->maxAge = $maxAge;

        return $this;
    }

   
   

   
}
