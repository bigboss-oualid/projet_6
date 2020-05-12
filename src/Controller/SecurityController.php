<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\PasswordUpdateType;
use App\Service\TokenManager;
use App\Service\UserNotification;
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

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
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
			'hasError' => $error !== null,
			'last_username' => $lastUsername
		]);
	}

	/**
	 * @Route("/logout", name="security.logout")
	 */
	public function logout() { }

	/**
	 * @Route("/forgotten-password", name="security.forgotten_password")
	 * @param Request          $request
	 * @param UserNotification $mailer
	 * @param TokenManager     $tokenManager
	 *
	 * @return Response
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function forgottenPassword(Request $request, UserNotification $mailer, TokenManager $tokenManager): Response
	{
		if ($request->isMethod('POST')) {

			$email = $request->request->get('_email');
			/* @var $user User */
			$user = $this->em->getRepository(User::class)->findOneByEmail($email);

			if ($user === null) {
				$this->addFlash('danger', "Aucun compte n'est associé à cette adresse e-mail: <strong> {$email}</strong>");
				return $this->redirectToRoute('blog.home');
			}

			$token = $tokenManager->createToken($user);
			$this->em->flush();

			$url = $this->generateUrl('security.reset_password', [
				'tokenCode' => $token->getTokenCode()],
				UrlGeneratorInterface::ABSOLUTE_URL
			);

			$mailer->notify($user, 'SnowTricks: Réinitialisation du mot de passe', $url);

			$this->addFlash('success', "Nous avons envoyé un lien à <strong>{$email}</strong>, pour rénitialiser votre mot de passe.<br/> Le lien expirera dans les deux heures suivantes !");

			return $this->redirectToRoute('security.login');
		}

		return $this->render('security/forgotten_password.html.twig');
	}

	/**
	 * @Route("/reset-password/{tokenCode}", name="security.reset_password")
	 * @param Request                      $request
	 * @param String                       $tokenCode
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param TokenManager                 $tokenManager
	 *
	 * @return Response
	 */
	public function resetPassword(Request $request, String $tokenCode, UserPasswordEncoderInterface $passwordEncoder, TokenManager $tokenManager): Response
	{
		$passwordUpdate = new PasswordUpdate();

		$form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/* @var $user User */
			$user =	$tokenManager->getUserFromToken($tokenCode);

			if ($user === null) {
				$this->addFlash('danger', "Le lien est Inconnu ou expiré, veuillez demander un nouveau lien !");
				return $this->redirectToRoute('security.login');
			}

			if($tokenManager->isTokenExpired()){

				$this->addFlash('danger', 'Le lien est expiré, veuillez demander un nouveau lien !');
				return $this->redirectToRoute('security.login');
			}

			$user->setHash($passwordEncoder->encodePassword($user, $request->request->get('password_update')['newPassword']));

			$this->em->persist($user);
			$tokenManager->deleteToken();
			$this->em->flush();

			$this->addFlash('success', 'Votre mot de passe est mis à jour, Veuillez vous connecter avec votre nouveau mot de passe');

			return $this->redirectToRoute('security.login');
		}

		return $this->render('security/reset_password.html.twig', [
			'form' => $form->createView(),
			'token' => $tokenCode]
		);
	}
}
