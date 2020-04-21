<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
	/**
	 * @Route("/", name="pages.home")
	 * @param TrickRepository $repository
	 *
	 * @return Response
	 */
	public function home(TrickRepository $repository): Response
	{
		$tricks = $repository->findAll();

		return $this->render('pages/home.html.twig', [
			'current_menu'    => 'home',
			'tricks' => $tricks
		]);
	}
}
