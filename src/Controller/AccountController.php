<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\Trick;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Service\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AccountController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
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
	 * @Route("/account/profile", name="account.profile")
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function profile(Request $request): Response
	{
		$user = $this->getUser();
		$form = $this->createForm(AccountType::class, $user);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {

			$user->getAvatar()->setUser($user);
			if($user->getAvatar()->getImageFile() == null){
				$user->setAvatar(null);
			}

			$this->em->flush();

			$this->addFlash('success', "Vos données personnelles ont été enregistré avec succès !");
			return $this->redirectToRoute('account.index');
		}

		return $this->render('account/profile.html.twig', [
			'current_menu'    => 'profile',
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/account/password-update", name="account.password")
	 *
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $encoder
	 *
	 * @return Response
	 */
	public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
	{
		$user = $this->getUser();
		$passwordUpdate = new PasswordUpdate();
		$form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){
				$form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
			} else {

				$hash = $encoder->encodePassword($user, $passwordUpdate->getNewPassword());
				$user->setHash($hash);

				$this->em->persist($user);
				$this->em->flush();

				$this->addFlash('success', "Votre mot de passe a bien été modifié !");
				return $this->redirectToRoute('account.index');
			}
		}

		return $this->render('account/password.html.twig', [
			'current_menu'    => 'profile',
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/account/page/{!page}/{offset<\d+>?null}", name="account.index", requirements={"page": "\d+"}, defaults={"page": 1})
	 *
	 * @param            $page
	 * @param            $offset
	 * @param Pagination $pagination
	 *
	 * @return Response
	 */
	public function myAccount($page, $offset, Pagination $pagination): Response
	{
		$user = $this->getUser();
		$pagination->setEntityClass(Trick::class)
			->setOffset($offset)
			->setCriteria(['author' => $user])
			->setCurrentPage($page)
			->setEntityTemplatePath('blog/include/_pagination_tricks.html.twig')
			->setButtonTemplatePath('partials/_pagination.html.twig');

		return $this->render('account/index.html.twig', [
			'current_menu'    => 'index',
			'user' => $user,
			'pagination' => $pagination,
		]);
	}

}
