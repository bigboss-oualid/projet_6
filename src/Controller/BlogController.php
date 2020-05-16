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
	public function show(Trick $trick, String $slug, Request $request, EntityManagerInterface $em)
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
		$commentForm = $this->createForm(CommentType::class, $comment);
		$rating = $em->getRepository(Rating::class)->findOneBy(['trick' =>$trick, 'author'=>$user]);

		if(!$rating){
			$rating = new Rating();
		}

		$ratingForm = $this->createForm(RatingType::class, $rating);

		$commentForm->handleRequest($request);
		$ratingForm->handleRequest($request);

		if ($ratingForm->isSubmitted() && $ratingForm->isValid()) {
			$rating->setTrick($trick)
				->setAuthor($user);
			$em->persist($rating);
			$em->flush();
			$this->addFlash(
				'success',
				"Votre èvaluation de diffuculté a bien é´te pris en compte!"
			);
		}

		if ($commentForm->isSubmitted() && $commentForm->isValid()) {
			$comment->setTrick($trick)
				->setAuthor($user);
			$em->persist($comment);
			$em->flush();
			$this->addFlash(
				'success',
				"Votre commentaire a bien été pris en compte !"
			);
		}

		return $this->render('blog/show.html.twig', [
			'controller_name' => 'BlogController',
			'current_menu'    => 'show',
			'trick'           => $trick,
			'comment_form'    => $commentForm->createView(),
			'rating_form'     => $ratingForm->createView()
		]);
	}
}
