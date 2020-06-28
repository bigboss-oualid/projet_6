<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\User;
use App\Service\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
	/**
	 * @Route("/user/{username}/page/{!page}/{offset<\d+>?null}", name="user.show", requirements={"username": "[a-z0-9\-]*", "page": "\d+"}, defaults={"page": 1})
	 *
	 * @param User       $user
	 * @param            $page
	 * @param            $offset
	 * @param Pagination $pagination
	 *
	 * @return Response
	 */
	public function index(User $user, $page, $offset, Pagination $pagination): Response
	{
		if($user == $this->getUser()){
			return $this->redirectToRoute('account.index');
		}

		$pagination->setEntityClass(Trick::class)
			->setOffset($offset)
			->setCriteria(['author' => $user])
			->setCurrentPage($page)
			->setRouteParameters(['username' => $user->getUsername()])
			->setEntityTemplatePath('blog/include/_pagination_tricks.html.twig')
			->setButtonTemplatePath('partials/_pagination.html.twig');

		return $this->render('account/index.html.twig', [
			'user' => $user ,
			'pagination' => $pagination,
		]);
	}
}
