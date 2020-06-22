<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\Token;
use App\Entity\User;
use App\Form\PasswordUpdateType;
use App\Form\UserType;
use App\Service\TokenManager;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
	/**
	 * @var EntityManagerInterface
	 */
	private $em;
	/**
	 * @var Mailer
	 */
	private $mailer;
	/**
	 * @var TokenManager
	 */
	private $tokenManager;

	public function __construct(EntityManagerInterface $em, Mailer $mailer, TokenManager $tokenManager)
	{
		$this->em = $em;
		$this->mailer = $mailer;
		$this->tokenManager = $tokenManager;
	}

	/**
	 * @Route("/login", name="security.login")
	 *
	 * @param AuthenticationUtils $utils
	 *
	 * @return Response
	 */
	public function login(AuthenticationUtils $utils):Response
	{
		$error = $utils->getLastAuthenticationError();
		$lastUsername = $utils->getLastUsername();

		return $this->render('security/login.html.twig',[
			'current_menu'    => 'login',
			'error' => $error,
			'last_username' => $lastUsername
		]);
	}

	/**
	 * @Route("/logout", name="security.logout")
	 */
	public function logout() { }

	/**
	 * @Route("/send-confirmation/{username}", name="security.send_confirm", requirements={"username": "[a-z0-9\-]*"})
	 * @param User   $user
	 *
	 * @return Response
	 */
	public function sendConfirmation(User $user): Response
	{
		$token = $user->getToken();
		$this->mailer->send(
			'emails/partials/confirmation_account.html.twig',
			'Confirmation de votre compte',
			[
				'user' => $user,
				'url'  => $token->getUrlActivation(),
				'confirmationCode' => $token->getConfirmationCode()
			]);
		$this->addFlash('success', "Un email de confirmation à été envoyé à <strong>{$user->getEmail()}</strong>.");

		return $this->redirectToRoute('security.confirm_account', [
			'username' => $user->getUsername(),
			'tokenCode' => $token->getTokenCode()
		]);
	}


	/**
	 * @Route("/register", name="security.register")
	 *
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $encoder
	 *
	 * @return Response
	 */
	public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {

			$user->getAvatar()->setUser($user);
			if($user->getAvatar()->getImageFile() == null){
				$user->setAvatar(null);
			}
			$user->setHash($encoder->encodePassword($user, $user->getHash()));

			//Create token to activate new account
			$token = $this->tokenManager->createToken($user, WITH_CONFIRMATION);

			$url = $this->generateUrl('security.confirm_account', ['tokenCode' => $token->getTokenCode(), 'username' => $user->getUsername() ],UrlGeneratorInterface::ABSOLUTE_URL);

			$token->setUrlActivation($url);

			$this->em->persist($user);
			$this->em->flush();

			$this->mailer->send('emails/partials/confirmation_account.html.twig','Confirmation de votre compte', ['user' => $user, 'url'  => $url, 'confirmationCode' => $token->getConfirmationCode()]);

			$this->addFlash('success', "!!!  Félicitations  !!!<br/> Votre compte a bien été créé ! <br/>Un email de confirmation à été envoyé à <strong>{$user->getEmail()}</strong>!");
			return $this->redirectToLogin();
		}

		return $this->render('security/register.html.twig', [
			'current_menu'    => 'register',
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/confirm-account/{username}/{tokenCode}", name="security.confirm_account", requirements={"username": "[a-z0-9\-]*"})
	 *
	 * @param User                   $user
	 * @param String                 $tokenCode
	 * @param Request                $request
	 *
	 * @return Response
	 */
	public function confirmAccount(User $user, String $tokenCode, Request $request): Response
	{
		if ($request->isMethod('POST')) {
			if(!$user->isEnabled()) {
				$userToken = $user->getToken();
				$confirmationCode = $request->request->get('_confirmationCode');

				//Check token link is valid
				if($userToken->getTokenCode() === $tokenCode) {

					if($confirmationCode != $userToken->getConfirmationCode()){

						$this->addFlash('danger', "le code que vous avez entré <strong>[ $confirmationCode]</strong> est invalide<br/>!!! Vérifier votre code svp !!!");
						return $this->redirectToRoute('security.confirm_account', ['username'=>$user->getUsername(), 'tokenCode'=> $tokenCode]);
					}

					$this->activateUser($user, $userToken);

					$this->em->persist($user);
					$this->em->flush();

					$this->addFlash('info', "Votre compte est activé, désormais vous pouvez vous connectez!");

				}else {
					$this->addFlash('danger', "Une erreur est survenue, le token <strong>[ {$tokenCode} ]</strong> du lien d'activation est incorrect!");
				}
			} else {
				$this->addFlash('warning', "!!!  Cher utilisateur <strong>{$user->getFullName()}</strong> !!!<br/> Votre compte est déjà activé, désormais vous pouvez vous connecter!");
			}

			return $this->redirectToLogin();
		}

		return $this->render('security/confirm_account.html.twig',[
			'current_menu'    => 'login'
		]);

	}

	/**
	 * @Route("/forgotten-password", name="security.forgotten_password")
	 *
	 * @param Request      $request
	 *
	 * @return Response
	 */
	public function forgottenPassword(Request $request): Response
	{
		if ($request->isMethod('POST')) {

			$email = $request->request->get('_email');
			/* @var $user User */
			$user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

			if ($user === null) {
				$this->addFlash('danger', "Aucun compte n'est associé à cette adresse e-mail: <strong> {$email}</strong>");
				return $this->redirectToLogin();
			}

			$token = $this->tokenManager->createToken($user);
			$this->em->flush();

			$url = $this->generateUrl('security.reset_password', ['tokenCode' => $token->getTokenCode()],UrlGeneratorInterface::ABSOLUTE_URL);

			$this->mailer->send('emails/partials/reset_password.html.twig','Réinitialisation du mot de passe', ['user' => $user, 'url'  => $url]);

			$this->addFlash('success', "Nous avons envoyé un lien à <strong>{$email}</strong>, pour rénitialiser votre mot de passe.<br/> Le lien expirera dans les deux heures suivantes!");

			return $this->redirectToLogin();
		}

		return $this->render('security/forgotten_password.html.twig',[
			'current_menu'    => 'login'
		]);
	}

	/**
	 * @Route("/reset-password/{tokenCode}", name="security.reset_password")
	 *
	 * @param Request                      $request
	 * @param String                       $tokenCode
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 *
	 * @return Response
	 */
	public function resetPassword(Request $request, String $tokenCode, UserPasswordEncoderInterface $passwordEncoder): Response
	{
		$passwordUpdate = new PasswordUpdate();

		$form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/* @var $user User */
			$user =	$this->tokenManager->getUserFromToken($tokenCode);

			if ($user === null || $this->tokenManager->isTokenExpired()) {

				$this->addFlash('danger', "Le lien est incorrect ou expiré, veuillez demander un nouveau lien, afin de  réinitialiser votre mot de passe !");

			} else {

				$user->setHash($passwordEncoder->encodePassword($user, $request->request->get('password_update')['newPassword']));

				$this->em->persist($user);
				$this->tokenManager->deleteToken();
				$this->em->flush();

				$this->addFlash('success', 'Votre mot de passe est mis à jour, Veuillez vous connecter avec votre nouveau mot de passe');
			}

			return $this->redirectToLogin();
		}

		return $this->render('security/reset_password.html.twig', [
				'current_menu'    => 'login',
				'form' => $form->createView(),
				'token' => $tokenCode]
		);
	}

	private function activateUser(User $user, Token $userToken){
		$user->setToken(null);
		$userToken->setUser(null);
		$this->em->remove($userToken);
		$user->setEnabled(true);
	}

	private function redirectToLogin(){
		return $this->redirectToRoute('security.login');
	}
}


