<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap( {"illustration" = "Illustration", "avatar" = "Avatar"} )
 */
abstract class Picture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

	/**
	 * @var File|null
	 * @Assert\Image(
	 *     mimeTypes={"image/jpeg", "image/png", "image/jpg"},
	 *     maxSize="2M"
	 * )
	 */
	private $imageFile;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $altName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $filename;

	/**
	 * save name before remove from DB
	 * @var string
	 */
    protected $tempFilename;

	public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return null|File
	 */
	public function getImageFile(): ?File
	{
		return $this->imageFile;
	}

	/**
	 * @param null|File $imageFile
	 *
	 * @return Picture
	 */
	public function setImageFile(?File $imageFile): self
	{
		if($imageFile){
			$originName = $imageFile->getClientOriginalName();
			$this->altName = $originName;
		}
		$this->imageFile = $imageFile;

		return $this;
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

    public function getAltName(): ?string
    {
        return $this->altName;
    }

    public function setAltName(string $altName): self
    {
        $this->altName = $altName;
        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
	    if($this->imageFile = null){}

        return $this;
    }

	/**
	 * @return string
	 */
	public function getTempFilename()
	{
		return $this->tempFilename;
	}

	/**
	 * @param string $tempFilename
	 *
	 * @return Picture
	 */
	public function setTempFilename(string $tempFilename): self
	{
		$this->tempFilename = $tempFilename;

		return $this;
	}

}
