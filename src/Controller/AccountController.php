<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
	/**
	 * @Route("/account/profile", name="account.profile")
	 * @IsGranted("ROLE_USER")
	 *
	 * @param Request                $request
	 * @param EntityManagerInterface $em
	 *
	 * @return Response
	 */
	public function profile(Request $request, EntityManagerInterface $em ): Response
	{
		$user = $this->getUser();

		$form = $this->createForm(AccountType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$user->getAvatar()->setUser($user);
			if($user->getAvatar()->getImageFile() == null){
				$user->setAvatar(null);
			}

			$em->flush();

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
	 * @IsGranted("ROLE_USER")
	 *
	 * @param Request                      $request
	 * @param EntityManagerInterface       $em
	 * @param UserPasswordEncoderInterface $encoder
	 *
	 * @return Response
	 */
	public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder,  EntityManagerInterface $em): Response {
		$user = $this->getUser();

		$passwordUpdate = new PasswordUpdate();

		$form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){
				$form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
			} else {
				//$newPassword = $passwordUpdate->getNewPassword();

				$hash = $encoder->encodePassword($user, $passwordUpdate->getNewPassword());
				$user->setHash($hash);

				$em->persist($user);
				$em->flush();
				$this->addFlash('success', "Votre mot de passe a bien été modifié !");

				return $this->redirectToRoute('account.profile');
			}
		}

		return $this->render('account/password.html.twig', [
			'current_menu'    => 'profile',
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/account", name="account.index", requirements={"username": "[a-z0-9\-]*"})
	 * @IsGranted("ROLE_USER")
	 *
	 * @return Response
	 */
	public function myAccount(): Response
	{
		return $this->render('user/index.html.twig', [
			'current_menu'    => 'profile',
			'user' => $this->getUser()
		]);
	}

}
