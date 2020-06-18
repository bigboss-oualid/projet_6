<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IllustrationRepository")
 */
class Illustration extends Picture
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="illustrations")
     */
    private $trick;

	public function getTrick(): Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
    	if($trick)
            $this->trick = $trick;

        return $this;
    }
}
