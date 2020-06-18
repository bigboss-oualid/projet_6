<?php

namespace App\Service;

use App\Entity\User;
use Twig\Environment;

class Mailer
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
	 * Mailer constructor.
	 *
	 * @param \Swift_Mailer $mailer
	 * @param Environment   $renderer
	 */
	public function __construct(\Swift_Mailer $mailer, Environment $renderer)
	{
		$this->mailer = $mailer;
		$this->renderer = $renderer;
	}

	public function send(String $template, String $subject, Array $data)
	{
		$message = (new \Swift_Message('SnowTricks: ' . $subject))
			->setFrom('noreply@snowtricks.com')
			->setTo($data['user']->getEmail())
			->setReplyTo('admin@snowtricks.com')
			->setBody($this->renderer->render($template, [
				'fullName' => $data['user']->getFullName(),
				'url'  => $data['url'],
				'confirmationCode' => isset($data['confirmationCode'])? $data['confirmationCode']: ''
			]), 'text/html');

		$this->mailer->send($message);
	}

}