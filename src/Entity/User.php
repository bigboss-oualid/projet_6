<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Un autre utilisateur s'est déjà inscrit avec cette email, merci de la modifier"
 * )
 * @UniqueEntity(
 *     fields={"username"},
 *     message="Un autre utilisateur s'est déjà inscrit avec ce username, merci de la modifier"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre nom de famille !")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre prénom !")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=25, unique=true, unique=true)
     * @Assert\NotBlank(message="Vous devez renseigner un username !")
     * @Assert\Length(min=5, max=25, minMessage="Username trop court, Il doit faire au moins 5 caractères !", maxMessage="Username trop long, Il doit faire au max 25 caractères !")
     * @Assert\Regex(
     *     pattern="/^[a-z]/",
     *     message="Le username devrait commencer par une lettre !"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-z0-9-]*$/",
     *     message="Le username devrait contenir seulement des lettres miniscules, des chiffres et le caractère - !"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(message="Veuillez renseigner un email valide !")
     */
    private $email;

	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 * @Assert\Length(min=8, minMessage="Votre mot de passe doit faire au moins 8 caractères !", )
	 * @Assert\Regex(
	 *     pattern="/.*^(?=.*[A-Z]).*$/",
	 *     message="Le mot de passe devrait contenir au moindes lettres en majuscules et en minuscules !"
	 * )
	 * @Assert\Regex(
	 *     pattern="/.*^(?=.*\d).*$/",
	 *     message="Le mot de passe devrait contenir des nombres !"
	 * )
	 * @Assert\Regex(
	 *     pattern="/.*^(?=.*\W).*$/",
	 *     message="Le mot de passe devrait contenir des caractères spéciaux !"
	 * )
	 */
    //pattern="/.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).*$/"
    private $hash;

	/**
	 * @Assert\EqualTo(propertyPath="hash", message="Vous avez entrer deux mots de passe différents !")
	 */
    public $passwordConfirm;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Avatar", mappedBy="user", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Trick", mappedBy="author")
     */
    private $tricks;

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
	    $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

	public function getFullName(): String {
		return ucwords("{$this->getFirstName()} {$this->getLastName()}");
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

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

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): self
    {
	    //$avatar->setTitle('avatar');
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->setAuthor($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->contains($trick)) {
            $this->tricks->removeElement($trick);
            // set the owning side to null (unless already changed)
            if ($trick->getAuthor() === $this) {
                $trick->setAuthor(null);
            }
        }

        return $this;
    }


	public function getRoles(): array
	{
		return ['ROLE_USER'];
	}

	public function getPassword():? String
	{
		return $this->hash;
	}

	public function getSalt() {}

	public function eraseCredentials() {}
}