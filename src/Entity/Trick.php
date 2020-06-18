<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrickRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"title"},
 *     message="Une autre figure possède déjà ce titre, merci de le modifier"
 * )
 */
class Trick
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 * @Assert\NotBlank(message="Vous devez renseigner un titre !")
	 * @Assert\Length(min=3, max=255, minMessage="Titre trop court, Il doit faire au moins 3 caractères !", maxMessage="Titre trop long, Il doit faire au max 255 caractères ! !")
	 */
	private $title;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @Assert\Length(min=100, minMessage="La description est trop court, Il doit faire au moins 100 caractères !", )
	 */
	private $description;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Illustration", mappedBy="trick", cascade={"persist"}, orphanRemoval=true)
	 * @Assert\Valid()
	 */
	private $illustrations;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="trick", cascade={"persist"}, orphanRemoval=true)
	 * @Assert\Valid()
	 */
	private $videos;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $slug;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $published = 1;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tricks")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserUpdateTrick", mappedBy="trick", cascade={"persist"}, orphanRemoval=true)
     */
    private $updatedBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="trick", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rating", mappedBy="trick", orphanRemoval=true)
     */
    private $ratings;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", fetch="EAGER", inversedBy="tricks",cascade={"persist"})
     */
    private $groups;

	public function __construct()
	{
	   $this->createdAt = new \DateTime();
	   $this->illustrations = new ArrayCollection();
	   $this->videos = new ArrayCollection();
	   $this->updatedBy = new ArrayCollection();
	   $this->comments = new ArrayCollection();
	   $this->ratings = new ArrayCollection();
	   $this->groups = new ArrayCollection();
	}

	public function getId(): ?int
	{
	    return $this->id;
	}

	public function getTitle(): ?string
	{
	    return $this->title;
	}

	public function setTitle(?string $title): self
	{
	    $this->title = $title;

	    return $this;
	}

	public function getDescription(): ?string
	{
	    return $this->description;
	}

	public function setDescription(?string $description): self
	{
		$this->description = $description;

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

	/**
	 * @return Collection|Illustration[]
	 */
	public function getIllustrations(): Collection
	{
	    return $this->illustrations;
	}

	public function addIllustration(Illustration $illustration): self
	{
	    if (!$this->illustrations->contains($illustration)) {
	        $this->illustrations[] = $illustration;
	        $illustration->setTrick($this);
	    }

	    return $this;
	}

	public function removeIllustration(Illustration $illustration): self
	{
	    if ($this->illustrations->contains($illustration)) {
	          $this->illustrations->removeElement($illustration);
	          // set the owning side to null (unless already changed)
	          if ($illustration->getTrick() === $this) {
	              $illustration->setTrick(null);
	          }
	    }

	    return $this;
	}

	/**
	 * @return Collection|Video[]
	 */
	public function getVideos(): Collection
	{
	    return $this->videos;
	}

	/**
	 * @param Video $video
	 *
	 * @return Trick
	 */
	public function addVideo(Video $video): self
	{
	    if (!$this->videos->contains($video)) {
	        $this->videos[] = $video;
	        $video->setTrick($this);
	    }

	    return $this;
	}

	public function removeVideo(Video $video): self
	{
	    if ($this->videos->contains($video)) {
	        $this->videos->removeElement($video);
	        // set the owning side to null (unless already changed)
	        if ($video->getTrick() === $this) {
	            $video->setTrick(null);
	        }
	    }

	    return $this;
	}

	public function getSlug(): ?string
	{
	    return $this->slug;
	}

	/**
	 * @ORM\PrePersist
	 * @ORM\PreUpdate()
	 *
	 * @param EventArgs $event
	 *
	 * @return void
	 */
	public function createSlug(EventArgs $event): void
	{
	  //create slug if trick is new or his title is modified
	    if($this->id == null || (isset($event->getEntityChangeSet()['title']))){
		    $this->slug = $this->slugify($this->title);
	    }
	}

	public function isPublished(): ?bool
	{
	    return $this->published;
	}

	public function setPublished(bool $published): self
	{
	    $this->published = $published;

	    return $this;
	}

	public function getAuthor(): ?User
	{
	    return $this->author;
	}

	public function setAuthor(?User $author): self
	{
	    $this->author = $author;

        return $this;
	}

    /**
     * @return Collection|UserUpdateTrick[]
     */
    public function getUpdatedBy(): Collection
    {
        return $this->updatedBy;
    }

    public function addUpdatedBy(UserUpdateTrick $updatedBy): self
    {
        if (!$this->updatedBy->contains($updatedBy)) {
            $this->updatedBy[] = $updatedBy;
	        $updatedBy->setTrick($this);
        }

        return $this;
    }

    public function removeUpdatedBy(UserUpdateTrick $updatedBy): self
    {
        if ($this->updatedBy->contains($updatedBy)) {
            $this->updatedBy->removeElement($updatedBy);
            // set the owning side to null (unless already changed)
            if ($updatedBy->getTrick() === $this) {
	            $updatedBy->setTrick(null);
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
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
	        $rating->setTrick($this);
        }

        return $this;
    }

	/**
	 * get Average of Ratings
	 *
	 * @return Int
	 */
	public function countComment(): Int{
        if($this->comments){
	        return count($this->comments);
        }

        return 0;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getTrick() === $this) {
	            $rating->setTrick(null);
            }
        }

        return $this;
    }

	/**
	 * get Average of Ratings
	 *
	 * @return Int
	 */
	public function getAvgRatings(): Int{
	    if(count($this->ratings))
	    {
	        $sum = array_reduce($this->ratings->toArray(), function($total, Rating $rating) {
	            return $total + $rating->getRating();
	        }, 0);
	        return $sum / count($this->ratings);
	    }

	    return 0;
	}

	/**
	 * Initialize le slug
	 *
	 * @param string $string
	 * @param string $delimiter
	 *
	 * @return string
	 */
	private function slugify(String $string, String $delimiter = '-'): string {
	    $oldLocale = setlocale(LC_ALL, '0');
	    setlocale(LC_ALL, 'en_US.UTF-8');
	    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
	    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	    $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
	    $clean = strtolower($clean);
	    $clean = trim($clean, $delimiter);
	    setlocale(LC_ALL, $oldLocale);
	    return $clean;
	}

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(?Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addTrick($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeTrick($this);
        }

        return $this;
    }
}
