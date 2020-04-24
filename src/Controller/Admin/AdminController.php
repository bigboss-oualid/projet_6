<?php

namespace App\Controller\Admin;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

	/**
	 * @var TrickRepository
	 */
	private $repository;
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	public function __construct(TrickRepository $repository, EntityManagerInterface $em)
	{
		$this->repository = $repository;
		$this->em = $em;
	}

	/**
	 * @Route("/tricks", name="admin.index")
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

	/**
	 * @Route("/tricks/new", name="admin.trick.create")
	 * @Route("/tricks/{id}/edit", name="admin.trick.edit", requirements={"slug": "[a-z0-9\-]*", "id": "\d+"})
	 * @param Trick|null $trick
	 * @param Request    $request
	 *
	 * @return Response
	 */
    public function formTrick(Trick $trick = null, Request $request):Response
    {
    	if(!$trick){
		    $trick = new Trick();
		    $flash = " enregistrée";
	    }else{
		    $flash = " modifiée";
	    }

		$form = $this->createForm(TrickType::class, $trick);

		$form->handleRequest($request);
	    if ($form->isSubmitted() && $form->isValid()) {
		    $this->em->persist($trick);
		    $this->em->flush();
		    $this->addFlash('success', "la figure <strong>{$trick->getTitle()}</strong> a bien été " . $flash);
		    return $this->redirectToRoute('admin.index');
	    }

		return $this->render('admin/form/trick.html.twig', [
			'formTrick' => $form->createView(),
			'editMode'  => $trick->getId() !== null
		]);
    }
	/**
	 * @Route("/tricks/delete/{id}", name="admin.trick.delete", methods="DELETE", requirements={"id": "\d+"})
	 * @param Trick   $trick
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function delete(Trick $trick, Request $request):RedirectResponse
	{

		if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token'))) {
			$this->em->remove($trick);
			$this->em->flush();
			$this->addFlash('success', 'Figure supprimé avec succès');
		}
		return $this->redirectToRoute('admin.index');
	}
}
