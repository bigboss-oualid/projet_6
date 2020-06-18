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
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    public function getTrick(): Trick
    {
        return $this->trick;
    }

    public function setTrick(Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
