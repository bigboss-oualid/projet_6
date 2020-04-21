<?php

namespace App\Controller\Admin;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
	/**
	 * @Route("/admin", name="admin.index")
	 *
	 * @param TrickRepository $repository
	 * @return  Response
	 */
    public function index(TrickRepository $repository): Response
    {
	    $tricks = $repository->findAll();

	    return $this->render('admin/index.html.twig', [
		    'current_menu'    => 'home',
		    'tricks' => $tricks
	    ]);
    }
}
