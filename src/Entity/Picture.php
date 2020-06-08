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
	 *     maxSize="2M",
	 *     mimeTypesMessage = "Veuillez télécharger une image valide",
	 *     maxSizeMessage = "Veuillez télécharger une image <= 2Mo"
	 *
	 * )
	 */
	private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $path;

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
		//resolve seriliaze problem
		if(!$this->imageFile){
			$this->tempFilename = $this->path;
			$this->path = "";
		}

		$this->imageFile = $imageFile;

		return $this;
	}

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
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
