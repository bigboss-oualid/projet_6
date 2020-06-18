<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
	/**
	 * @Route("/user/{username}", name="user.show", requirements={"username": "[a-z0-9\-]*"})
	 * @param User $user
	 *
	 * @return Response
	 */
	public function index(User $user): Response
	{
		return $this->render('user/index.html.twig', [
			'user' => $user,
		]);
	}
}
