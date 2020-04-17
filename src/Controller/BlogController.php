<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
	/**
	 * @Route("/", name="blog.home")
	 */
	public function home()
	{
		return $this->render('blog/home.html.twig', [
			'controller_name' => 'BlogController',
			'current_menu'    => 'home'
		]);
	}
}
