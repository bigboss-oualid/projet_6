<?php

namespace App\Service;

use App\Entity\User;
use Twig\Environment;

class UserNotification
{
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

	/**
	 * @var Environment
	 */
	private $renderer;

	/**
	 * UserNotification constructor.
	 *
	 * @param \Swift_Mailer $mailer
	 * @param Environment   $renderer
	 */
	public function __construct(\Swift_Mailer $mailer, Environment $renderer)
	{
		$this->mailer = $mailer;
		$this->renderer = $renderer;
	}

	/**
	 * @param User   $user
	 * @param String $subject
	 *
	 * @param String $url
	 *
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function notify(User $user, String $subject, String $url)
	{
		$message = (new \Swift_Message($subject))
			->setFrom('noreply@snowtricks.com')
			->setTo($user->getEmail())
			->setReplyTo('admin@snowtricks.com')
			->setBody($this->renderer->render('emails/partials/reset_password.html.twig', [
				'user' => $user,
				'url'  => $url
			]), 'text/html');

		$this->mailer->send($message);
	}

}