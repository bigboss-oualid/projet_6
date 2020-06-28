<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TokenRepository")
 */
class Token
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tokenCode;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="token", fetch="EAGER", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $confirmationCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $urlActivation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTokenCode(): ?string
    {
        return $this->tokenCode;
    }

    public function setTokenCode(string $tokenCode): self
    {
        $this->tokenCode = $tokenCode;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getConfirmationCode(): ?int
    {
        return $this->confirmationCode;
    }

    public function generateConfirmationCode(Int $min, Int $max): self
    {
		$this->confirmationCode = rand($min, $max);

        return $this;
    }

    public function getUrlActivation(): ?string
    {
        return $this->urlActivation;
    }

    public function setUrlActivation(?string $urlActivation): self
    {
        $this->urlActivation = $urlActivation;

        return $this;
    }
}
