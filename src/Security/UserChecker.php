<?php
namespace App\Security;

use App\Exception\AccountDisabledException;
use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
	public function checkPreAuth(UserInterface $user)
	{
		if (!$user instanceof AppUser) {
			return;
		}

		// User account is deactivated.
		if (!$user->isEnabled()) {
			throw new AccountDisabledException("Votre compte n'est pas activé!");
		}
	}

	public function checkPostAuth(UserInterface $user){}
}