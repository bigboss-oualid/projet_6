<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Rating;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\RatingType;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
	/**
	 * @Route("/", name="blog.home")
	 *
	 * @param Pagination $pagination
	 * @param Request    $request
	 *
	 * @return Response | JsonResponse
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function home(Pagination $pagination, Request $request)
	{
		$pagination->setEntityClass(Trick::class);
		if($request->isXmlHttpRequest()){
			$pagination->setEntityTemplatePath('blog/include/_pagination_tricks.html.twig');

			return new JsonResponse($pagination->renderView(), 200, [], true);
		}

		$pagination->setRoute('/');

		return $this->render('blog/home.html.twig', [
			'current_menu'  => 'home',
			'pagination' => $pagination,
		]);
	}


	/*public function loadTricks(Pagination $pagination, Request $request): JsonResponse{

		if($request->isXmlHttpRequest()){
			$pagination->setEntityClass(Trick::class)
				->setEntityTemplatePath('blog/include/_pagination_tricks.html.twig');

			return new JsonResponse($pagination->renderView(), 200, [], true);
		}

		return new JsonResponse([
			'type' => 'error',
			'message' => 'request is not with ajax'
		]);
	}*/

	/**
	 * @Route("/trick/{id}/add-comment", name="add_comment", requirements={"id": "\d+"})
	 * @IsGranted("ROLE_USER")
	 *
	 * @param Trick                  $trick
	 * @param EntityManagerInterface $em
	 * @param Request                $request
	 *
	 * @return JsonResponse
	 */
	public function addComment(Trick $trick, EntityManagerInterface $em, Request $request): JsonResponse{
		$user = $this->getUser();

		if($request->isXmlHttpRequest()) {
			$comment = new Comment();
			$form = $this->createForm(CommentType::class, $comment);
			$form->handleRequest($request);

			if(!$form->isValid()) {
				$errors = $this->getErrorsFromForm($form);
				$data = [
					'type' => 'validation_error',
					'title' => 'There was a validation error',
					'errors' => $errors
				];
				return new JsonResponse($data, 400);
			}

			$comment->setTrick($trick)->setAuthor($user);
			$em->persist($comment);
			$em->flush();
			$html = $this->renderView('blog/include/_pagination_comment.html.twig', ['comments' => [$comment]]);

			return new JsonResponse(['comment' => $html], 200);
		}

		return new JsonResponse([
			'type' => 'error',
			'title' => 'Request is not isXmlHttpRequest'
		], 500);
	}

	/**
	 * @Route("/tricks/{slug}/{id}", name="blog.show", requirements={"slug": "[a-z0-9\-]*", "id": "\d+"})
	 * @param Trick                  $trick
	 * @param String                 $slug
	 * @param Request                $request
	 * @param EntityManagerInterface $em
	 * @param Pagination             $pagination
	 *
	 * @return Response
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function show(Trick $trick, String $slug, Request $request, EntityManagerInterface $em, Pagination $pagination): Response
	{
		if ($trick->getSlug() != $slug) {
			return $this->redirectToRoute('blog.show', [
				'slug' => $trick->getSlug(),
				'id'   => $trick->getId()
			], 301);
		}

		$pagination->setEntityClass(Comment::class)->setCriteria(['trick' =>$trick]);
		if($request->isXmlHttpRequest()){
			$pagination->setEntityTemplatePath('blog/include/_pagination_comment.html.twig');

			return new JsonResponse($pagination->renderView(), 200, [], true);
		}

		$pagination->setRoute('/tricks/' . $slug . '/'. $trick->getId());

		/** @var User $user */
		if($user = $this->getUser()){

			$comment = new Comment();

			$rating = $em->getRepository(Rating::class)->findOneBy(['trick' =>$trick, 'author'=>$user]);
			if(!$rating){
				$rating = new Rating();
			}

			$commentForm = $this->createForm(CommentType::class, $comment);

			$ratingForm = $this->createForm(RatingType::class, $rating);
			$ratingForm->handleRequest($request);

			if ($ratingForm->isSubmitted() && $ratingForm->isValid()) {
				$rating->setTrick($trick)
					->setAuthor($user);
				$em->persist($rating);
				$em->flush();
				$this->addFlash('success', "Votre èvaluation de diffuculté a bien é´te pris en compte!");
			}

			return $this->render('blog/show.html.twig', [
				'current_menu'    => 'show',
				'trick'           => $trick,
				'rating_form'     => $ratingForm->createView(),
				'comment_form'    => $commentForm->createView(),
				'pagination' => $pagination
			]);
		}
		return $this->render('blog/show.html.twig', [
			'current_menu'    => 'show',
			'trick'           => $trick,
			'pagination' => $pagination,
		]);
	}

	/**
	 * use errors in ajax request
	 * @param FormInterface $form
	 *
	 * @return array
	 */
	private function getErrorsFromForm(FormInterface $form)
	{
		$errors = array();
		foreach ($form->getErrors() as $error) {
			$errors[] = $error->getMessage();
		}
		foreach ($form->all() as $childForm) {
			if ($childForm instanceof FormInterface) {
				if ($childErrors = $this->getErrorsFromForm($childForm)) {
					$errors[$childForm->getName()] = $childErrors;
				}
			}
		}
		return $errors;
	}
}
