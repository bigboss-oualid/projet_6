<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 16/06/2020
 * Time: 20:17
 */

namespace App\Listener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectUserListener
{
	private $tokenStorage;
	private $router;

	public function __construct(TokenStorageInterface $t, RouterInterface $r)
	{
		$this->tokenStorage = $t;
		$this->router = $r;
	}

	public function onKernelRequest(RequestEvent $event)
	{
		if ($this->isUserLogged() && $event->isMasterRequest()) {
			$currentRoute = $event->getRequest()->attributes->get('_route');

			if ($this->isAuthenticatedUserOnAnonymousPage($currentRoute)) {
				$response = new RedirectResponse($this->router->generate('blog.home'));
				$event->setResponse($response);
			}
		}
	}

	private function isUserLogged()
	{
		if (!$token = $this->tokenStorage->getToken()) {
			return null;
		}

		//change if you need user !$token->getUser() instanceof User
		if (!is_object($token->getUser())) {
			return null;
		}

		return true;
	}

	private function isAuthenticatedUserOnAnonymousPage($currentRoute)
	{
		return in_array(
			$currentRoute,
			['security.login', 'security.send_confirm', 'security.register', 'security.confirm_account', 'security.forgotten_password', 'security.reset_password']
		);
	}
}