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
	 * @var EntityManagerInterface
	 */
	private $em;
	/**
	 * @var Pagination
	 */
	private $pagination;

	public function __construct(EntityManagerInterface $em, Pagination $pagination)
	{
		$this->em = $em;
		$this->pagination = $pagination;
	}

	/**
	 * @Route("/", name="blog.home")
	 *
	 * @param Request    $request
	 *
	 * @return Response | JsonResponse
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function home(Request $request)
	{
		$this->pagination->setEntityClass(Trick::class);
		//Load more tricks
		if($request->isXmlHttpRequest()){
			$this->pagination->setEntityTemplatePath('blog/include/_pagination_tricks.html.twig');

			return new JsonResponse($this->pagination->renderView(), 200, [], true);
		}

		$this->pagination->setRoute('/');

		return $this->render('blog/home.html.twig', [
			'current_menu'  => 'home',
			'pagination' => $this->pagination,
		]);
	}

	/**
	 * determine value of offset only for next page, for previous page offset should be determined in service pagination in function getData()
	 *
	 * @Route("/tricks/page/{!page}/{offset<\d+>?null}",  name="blog.tricks", requirements={"page": "\d+"}, defaults={"page": 1})
	 *
	 * @param            $page
	 * @param            $offset
	 *
	 * @return Response | JsonResponse
	 */
	public function tricks($page, $offset)
	{
		$this->pagination->setEntityClass(Trick::class)
			->setOffset($offset)
			->setCurrentPage($page)
			->setEntityTemplatePath('blog/include/_pagination_tricks.html.twig')
			->setButtonTemplatePath('partials/_pagination.html.twig');

		return $this->render('blog/tricks.html.twig', [
			'current_menu'    => 'tricks',
			'pagination' => $this->pagination,
		]);
	}

	/**
	 * @Route("/trick/{id}/add-comment", name="add_comment", requirements={"id": "\d+"})
	 * @IsGranted("ROLE_USER")
	 *
	 * @param Trick                  $trick
	 * @param Request                $request
	 *
	 * @return JsonResponse
	 */
	public function addComment(Trick $trick, Request $request): JsonResponse
	{
		/**@var User $user*/
		$user = $this->getUser();

		if($request->isXmlHttpRequest()) {
			$comment = new Comment();
			$form = $this->createForm(CommentType::class, $comment);
			$form->handleRequest($request);

			//add error if comment form is not valid
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
			$this->em->persist($comment);
			$this->em->flush();
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
	 *
	 * @param Trick                  $trick
	 * @param String                 $slug
	 * @param Request                $request
	 *
	 * @return Response
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function show(Trick $trick, String $slug, Request $request): Response
	{
		// Correct title for SEO
		if ($trick->getSlug() != $slug) {
			return $this->redirectToRoute('blog.show', [
				'slug' => $trick->getSlug(),
				'id'   => $trick->getId()
			], 301);
		}

		$this->pagination->setEntityClass(Comment::class)->setCriteria(['trick' =>$trick]);
		if($request->isXmlHttpRequest()){
			$this->pagination->setEntityTemplatePath('blog/include/_pagination_comment.html.twig');

			return new JsonResponse($this->pagination->renderView(), 200, [], true);
		}

		$this->pagination->setRoute('/tricks/' . $slug . '/'. $trick->getId());

		$data = [
			'current_menu'    => 'show',
			'trick'           => $trick,
			'pagination' => $this->pagination,
		];

		/** @var User $user */
		if($user = $this->getUser()){

			$forms = $this->handleCommentOrRating($trick, $user, $request);

			$data = array_merge($data,[
				'rating_form'     => $forms['rating']->createView(),
				'comment_form'    => $forms['comment']->createView(),
			]);
		}

		return $this->render('blog/show.html.twig', $data);
	}

	/**
	 * Use errors in ajax request
	 *
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
			if ($childForm instanceof FormInterface && $childErrors = $this->getErrorsFromForm($childForm)) {
				$errors[$childForm->getName()] = $childErrors;
			}
		}
		return $errors;
	}

	/**
	 * add comment & change or add Rating
	 *
	 * @param Trick   $trick
	 * @param User    $user
	 * @param Request $request
	 *
	 * @return array
	 */
	private function handleCommentOrRating(Trick $trick, User $user, Request $request): array
	{
		$comment = new Comment();

		$rating = $this->em->getRepository(Rating::class)->findOneBy(['trick' =>$trick, 'author'=>$user]);
		if(!$rating){
			$rating = new Rating();
		}

		$commentForm = $this->createForm(CommentType::class, $comment);

		$ratingForm = $this->createForm(RatingType::class, $rating);
		$ratingForm->handleRequest($request);

		if ($ratingForm->isSubmitted() && $ratingForm->isValid()) {
			$rating->setTrick($trick)
				->setAuthor($user);
			$this->em->persist($rating);
			$this->em->flush();
			$this->addFlash('success', "Votre èvaluation de diffuculté a bien é´te pris en compte!");
		}

		return ['rating' => $ratingForm, 'comment' => $commentForm];
	}
}
