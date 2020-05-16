<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Rating;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\RatingType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
		if($this->getUser()){
			$tricks = $repository->findAll();
		}else{
			$tricks = $repository->findBy([
				"published" => true
			]);
		}

		return $this->render('blog/home.html.twig', [
			'current_menu'    => 'home',
			'tricks' => $tricks
		]);
	}

	/**
	 * @Route("/tricks/{slug}/{id}", name="blog.show", requirements={"slug": "[a-z0-9\-]*", "id": "\d+"})
	 * @param Trick                  $trick
	 * @param String                 $slug
	 * @param Request                $request
	 * @param EntityManagerInterface $em
	 *
	 * @return Response
	 */
	public function show(Trick $trick, String $slug, Request $request, EntityManagerInterface $em): Response
	{
		if ($trick->getSlug() != $slug) {
			return $this->redirectToRoute('blog.show', [
				'slug' => $trick->getSlug(),
				'id'   => $trick->getId()
			], 301);
		}

		/** @var User $user */
		$user = $this->getUser();

		$comment = new Comment();

		$rating = $em->getRepository(Rating::class)->findOneBy(['trick' =>$trick, 'author'=>$user]);
		if(!$rating){
			$rating = new Rating();
		}

		$commentForm = $this->submitForm(CommentType::class, $trick, $user, $comment, "Votre commentaire a bien été pris en compte !", $em, $request);

		$ratingForm = $this->submitForm(RatingType::class, $trick, $user, $rating, "Votre èvaluation de diffuculté a bien é´te pris en compte!", $em, $request);

		if($commentForm->getData()->getId()){
			return $this->redirectToRoute('blog.show', [
				'slug' => $trick->getSlug(),
				'id'   => $trick->getId(),
				'_fragment' => 'comment'
			], 301);
		}

		return $this->render('blog/show.html.twig', [
			'current_menu'    => 'show',
			'trick'           => $trick,
			'rating_form'     => $ratingForm->createView(),
			'comment_form'    => $commentForm->createView()
		]);
	}

	private function submitForm(String $formType, Trick $trick,User $user, $persist, String $successMessage, EntityManagerInterface $em, Request $request){

		$form = $this->createForm($formType, $persist);

		if($request->isMethod('POST')){
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid()) {
				$persist->setTrick($trick)
					->setAuthor($user);
				$em->persist($persist);
				$em->flush();
				$this->addFlash('success', $successMessage );
			}
		}

		return $form;
	}
}
