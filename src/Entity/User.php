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
     * @Assert\Length(min=2, max=25, minMessage="Nom trop court, Il doit faire au moins 2 caractères !", maxMessage="Nom trop long, Il doit faire au max 25 caractères !")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre prénom !")
     * @Assert\Length(min=2, max=25, minMessage="Prénom trop court, Il doit faire au moins 2 caractères !", maxMessage="Prénom trop long, Il doit faire au max 25 caractères !")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=25, unique=true, unique=true)
     * @Assert\NotBlank(message="Vous devez renseigner un username !")
     * @Assert\Length(min=4, max=25, minMessage="Username trop court, Il doit faire au moins 5 caractères !", maxMessage="Username trop long, Il doit faire au max 25 caractères !")
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
	 *     message="Le mot de passe devrait contenir au moin une lettre en majuscule et en minuscule !"
	 * )
	 * @Assert\Regex(
	 *     pattern="/.*^(?=.*\d).*$/",
	 *     message="Le mot de passe devrait contenir des nombres !"
	 * )
	 * @Assert\Regex(
	 *     pattern="/.*^(?=.*\W).*$/",
	 *     message="Le mot de passe devrait contenir au moin  un caractère spécial!"
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

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserUpdateTrick", mappedBy="author", cascade={"persist"}, orphanRemoval=true)
     */
    private $updatedTricks;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Token", mappedBy="user", cascade={"persist", "remove"})
     */
    private $token;

    /**
     * @ORM\Column(type="boolean", options={"default": false}, nullable=true)
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rating", mappedBy="author", orphanRemoval=true)
     */
    private $ratings;

    public function __construct()
    {
        $this->tricks        = new ArrayCollection();
	    $this->createdAt     = new \DateTime();
        $this->userRoles     = new ArrayCollection();
        $this->updatedTricks = new ArrayCollection();
        $this->comments      = new ArrayCollection();
        $this->ratings       = new ArrayCollection();
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

	public function getFullName(): String
                              	{
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

	public function getToken(): ?Token
                                  {
                                      return $this->token;
                                  }

	public function setToken(?Token $token): self
                                  {
                                      $this->token = $token;
                              
                                      // set the owning side of the relation if necessary
                                      if ($token != null && $token->getUser() !== $this) {
                                          $token->setUser($this);
                                      }
                              
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
    	if($this->avatar == null){
    		$this->avatar = new Avatar();
    		$this->avatar->setFilename('defaultAvatar.png');
	    }
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): self
    {
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
         $roles = $this->userRoles->map(function (Role $role){
             return $role->getTitle();
         })->toArray();

         $roles[] = 'ROLE_USER';

          return array_unique($roles);
    }

	public function getPassword():? String
    {
        return $this->hash;
    }

	public function getSalt() {}

	public function eraseCredentials() {}

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|UserUpdateTrick[]
     */
    public function getUpdatedTricks(): Collection
    {
        return $this->updatedTricks;
    }

    public function addUpdatedTrick(UserUpdateTrick $updatedTricks): self
    {
        if (!$this->updatedTricks->contains($updatedTricks)) {
            $this->updatedTricks[] = $updatedTricks;
            $updatedTricks->setAuthor($this);
        }

        return $this;
    }

    public function removeUpdatedTrick(UserUpdateTrick $updatedTricks): self
    {
        if ($this->updatedTricks->contains($updatedTricks)) {
            $this->updatedTricks->removeElement($updatedTricks);
            // set the owning side to null (unless already changed)
            if ($updatedTricks->getAuthor() === $this) {
                $updatedTricks->setAuthor(null);
            }
        }

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
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

    public function addRating(Rating $rate): self
    {
        if (!$this->ratings->contains($rate)) {
            $this->ratings[] = $rate;
	        $rate->setAuthor($this);
        }

        return $this;
    }

    public function removeRating(Rating $rate): self
    {
        if ($this->ratings->contains($rate)) {
            $this->ratings->removeElement($rate);
            // set the owning side to null (unless already changed)
            if ($rate->getAuthor() === $this) {
	            $rate->setAuthor(null);
            }
        }

        return $this;
    }
}
