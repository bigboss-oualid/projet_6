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

		// user is deleted, show a generic Account Not Found message.
		if (!$user->isEnabled()) {
			$exception = new AccountDisabledException("Votre compte n'est pas activé!");
			throw $exception;
		}
	}

	public function checkPostAuth(UserInterface $user)
	{
		if (!$user instanceof AppUser) {
			return;
		}
	}
}