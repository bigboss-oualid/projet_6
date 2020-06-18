<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
	/**
	 * @Route("/", name="blog.home")
	 * @param TrickRepository $repository
	 *
	 * @return Response
	 */
	public function home(TrickRepository $repository): Response
	{
		$tricks = $repository->findBy([
			"published" => true
		]);

		return $this->render('blog/home.html.twig', [
			'current_menu'    => 'home',
			'tricks' => $tricks
		]);
	}

	/**
	 * @Route("/tricks/{slug}/{id}", name="blog.show", requirements={"slug": "[a-z0-9\-]*", "id": "\d+"})
	 * @param Trick   $trick
	 * @param String  $slug
	 * @return Response
	 */
	public function show(Trick $trick, String $slug)
	{
		if ($trick->getSlug() != $slug) {
			return $this->redirectToRoute('blog.show', [
				'slug' => $trick->getSlug(),
				'id'   => $trick->getId()
			], 301);
		}

		return $this->render('blog/show.html.twig', [
			'controller_name' => 'BlogController',
			'current_menu'    => 'show',
			'trick'           => $trick
		]);
	}
}
