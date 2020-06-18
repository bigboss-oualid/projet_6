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
 * @UniqueEntity("title")
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
     * @Assert\Length(min=5, max=255)
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(min=3, minMessage="Titre trop court !")
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="tricks", cascade={"persist"})
     */
    private $category;

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
    private $published;

    private $illustrationFiles;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
	    $this->createdAt = new \DateTime();
        $this->illustrations = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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
            /*if ($illustration->getTrick() === $this) {
                $illustration->setTrick(null);
            }*/
        }

        return $this;
    }

	/**
	 * @return array
	 */
	public function getIllustrationFiles():? array
    {
        return $this->illustrationFiles;
    }

	/**
	 * @param $illustrationFiles
	 *
	 * @return Trick
	 */
	public function setIllustrationFiles($illustrationFiles): self
    {
        foreach($illustrationFiles as $illustrationFile) {
            $illustration = new Illustration();
            $illustration->setImageFile($illustrationFile);
            $this->addIllustration($illustration);
        }
        $this->illustrationFiles = $illustrationFiles;
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
            /*if ($video->getTrick() === $this) {
                $video->setTrick(null);
            }*/
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
		if($this->id == null || (isset($event->getEntityChangeSet()['title'])))
			$this->slug = $this->slugify($this->title);
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

	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function updateDate()
	{
		$this->setUpdatedAt(new \Datetime());
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

}
