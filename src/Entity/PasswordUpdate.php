<?php

namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;


class PasswordUpdate
{

    private $oldPassword;

	/**
	 * @Assert\Length(min=8, minMessage="Votre mot de passe doit faire au moins 8 caractères !", )
	 * @Assert\Regex(
	 *     pattern="/.*^(?=.*[A-Z]).*$/",
	 *     message="Le mot de passe devrait contenir des lettres en majuscules et en minuscules !"
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
    private $newPassword;

	/**
	 * @Assert\EqualTo(propertyPath="newPassword", message="Vous avez entrer deux mots de passes diffèrents")
	 */
    private $confirmPassword;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
