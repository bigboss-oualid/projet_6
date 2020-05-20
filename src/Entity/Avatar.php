<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AvatarRepository")
 */
class Avatar extends Picture
{
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="avatar")
	 */
	private $user;

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(User $user): self
	{
		$this->user = $user;

		return $this;
	}
}
