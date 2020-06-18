<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
	const WIDTH = 130;
	const HEIGHT = 120;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Regex(
     *     pattern="/^<iframe.*?s*src=""http[s]?:(.*?)"".*?<\/iframe>$/",
     *     message="Code de la video n'est pas correct"
     * )
     */
    private $embedCode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="videos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmbedCode(): ?string
    {
        return $this->embedCode;
    }

    public function setEmbedCode(?string $embedCode): self
    {
        $this->embedCode = $this->adjustVideo($embedCode);

	    return $this;
    }

    public function getTrick(): Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

	/**
	 *  Adjust Height & Width
	 *
	 * @param String $embedCode
	 *
	 * @return String
	 */
	private function adjustVideo(String $embedCode): String {

		return preg_replace(
			array('/width="\d+"/i', '/height="\d+"/i'),
			array(sprintf('width="%d"', self::WIDTH), sprintf('height="%d"', self::HEIGHT)),
			$embedCode);
	}
}
