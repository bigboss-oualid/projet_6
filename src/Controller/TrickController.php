<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\UserUpdateTrick;
use App\Form\TrickType;
use App\Repository\UserUpdateTrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TrickController extends AbstractController
{
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @Route("/tricks/new", name="trick.create")
	 * @Route("/tricks/{slug}/{id}/edit", name="trick.edit", requirements={"slug": "[a-z0-9\-]*", "id": "\d+"})
	 * @IsGranted("ROLE_USER")
	 *
	 * @param Trick|null                $trick
	 * @param String                    $slug
	 * @param Request                   $request
	 *
	 * @param UserUpdateTrickRepository $repository
	 *
	 * @return Response
	 */
    public function formTrick(Trick $trick = null, String $slug=null, Request $request, UserUpdateTrickRepository $repository):Response
    {
	    /** @var User $user */
	    $user = $this->getUser();

    	if(!$trick){
		    $trick = new Trick();
		    $trick->setAuthor($user);
		    $flash = " enregistrée";
	    }else{
		    if ($trick->getSlug() != $slug) {
			    return $this->redirectToRoute('trick.edit', [
				    'slug' => $trick->getSlug(),
				    'id'   => $trick->getId()
			    ], 301);
		    }
		    $flash = " modifiée";
	    }

		$form = $this->createForm(TrickType::class, $trick);

		$form->handleRequest($request);
	    if ($form->isSubmitted() && $form->isValid()) {

	    	if($trick->getId() != 0){

			    $update = $repository->findOneBy([
			    	'author' => $user->getId(),
				    'trick' => $trick->getId()
			    ]);

			    if(!$update){
				    $update = new UserUpdateTrick($user, $trick);
				    $user->addUpdatedTrick($update);
				    $trick->addUpdatedBy($update);
			    }else{
				    $user->removeUpdatedTrick($update);
				    $trick->removeUpdatedBy($update);
				    $update = new UserUpdateTrick($user, $trick);
				    $user->addUpdatedTrick($update);
				    $trick->addUpdatedBy($update);
			    }

			    //$update->setUpdatedAt(new \DateTime());

		    }

		    $this->em->persist($trick);
		    $this->em->flush();

		    $this->addFlash('success', "la figure <strong>{$trick->getTitle()}</strong> a bien été " . $flash);

		    return $this->redirectToRoute('blog.home');
	    }

		return $this->render('form/trick.html.twig', [
			'formTrick' => $form->createView(),
			'editMode'  => $trick->getId() !== null
		]);
    }

	/**
	 * @Route("/tricks/{id}/delete", name="trick.delete", methods="DELETE", requirements={"id": "\d+"})
	 * @Security("is_granted('ROLE_USER') and user === trick.getAuthor()", message="Cette figure ne vous appartient pas, vous ne pouvez pas la supprimer")
	 *
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
			$this->addFlash(
				'success',
				"La figure <strong>{$trick->getTitle()}</strong> a bien été supprimée !"
			);
		}
		return $this->redirectToRoute('blog.home');
	}
}
