<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\Token;
use App\Entity\User;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
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
	 * @param Request                      $request
	 * @param Swift_Mailer                 $mailer
	 * @param TokenGeneratorInterface      $tokenGenerator
	 *
	 * @return Response
	 */
	public function forgottenPassword(Request $request, Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
	{

		if ($request->isMethod('POST')) {

			$email = $request->request->get('_email');

			/* @var $user User */
			$user = $this->em->getRepository(User::class)->findOneByEmail($email);

			if ($user === null) {
				$this->addFlash('danger', "Aucun compte n'est associé à cette adresse e-mail: <strong> {$email}</strong>");
				return $this->redirectToRoute('blog.home');
			}
			$token = $tokenGenerator->generateToken();

			try{
				$userToken = new Token();
				$userToken->setCreatedAt(new \DateTime());
				$userToken->setTokenCode($token);
				$userToken->setUser($user);
				$this->em->persist($userToken);
				$this->em->flush();
			} catch (\Exception $e) {
				$this->addFlash('warning', $e->getMessage());
				return $this->redirectToRoute('security.login');
			}

			$url = $this->generateUrl('security.reset_password', array('token' => $token));

			$message = (new \Swift_Message('Forgot Password'))
				->setFrom('g.ponty@dev-web.io')
				->setTo($user->getEmail())
				->setBody(
					"voici le token pour reseter votre mot de passe : " . $url,
					'text/html'
				);

			$mailer->send($message);

			$this->addFlash('success', "Nous avons envoyé un lien à <strong>{$email}</strong>, pour rénitialiser votre mot de passe.<br/> Le lien expirera dans les deux heures suivantes !");

			return $this->redirectToRoute('security.login');
		}

		return $this->render('security/forgotten_password.html.twig');
	}

	/**
	 * @Route("/reset-password/{token}", name="security.reset_password")
	 * @param Request                      $request
	 * @param String                       $token
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 *
	 * @return Response
	 * @throws \Exception
	 */
	public function resetPassword(Request $request, String $token, UserPasswordEncoderInterface $passwordEncoder)
	{
		$passwordUpdate = new PasswordUpdate();

		$form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$tokenRepository = $this->em->getRepository(Token::class);
			$userToken = $tokenRepository->findOneByTokenCode($token);

			/* @var $user User */
			$user = null;
			if($userToken)
				$user = $userToken->getUser();

			if ($user === null) {
				$this->addFlash('danger', 'Le lien est Inconnu ou expiré, veuillez demander un nouveau lien !');
				return $this->redirectToRoute('security.login');
			}

			$time= $userToken->getCreatedAt();
			$timeplus = new \DateInterval('PT2H');
			$time->add($timeplus);

			if(new \DateTime()>$time){

				$userToken->setUser(null);
				$this->em->remove($userToken);
				$this->em->flush();

				$this->addFlash('danger', 'Le lien est expiré, veuillez demander un nouveau lien !');
				return $this->redirectToRoute('security.login');
			}
			
			$userToken->setUser(null);
			$this->em->remove($userToken);

			$user->setHash($passwordEncoder->encodePassword($user, $request->request->get('password_update')['newPassword']));

			$this->em->flush();

			$this->addFlash('success', 'Votre mot de passe est mis à jour, Veuillez vous connecter avec votre nouveau mot de passe');

			return $this->redirectToRoute('security.login');
		}

		return $this->render('security/reset_password.html.twig', [
			'form' => $form->createView(),
			'token' => $token]
		);
	}
}
