<?php

namespace App\Service;


use App\Entity\Token;
use App\Entity\User;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

define('WITH_CONFIRMATION', true);

class TokenManager
{
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * @var Token
	 */
	private $token;
	/**
	 * @var TokenGeneratorInterface
	 */
	private $tokenGenerator;
	/**
	 * @var TokenRepository
	 */
	private $repository;


	public function __construct(EntityManagerInterface $em, TokenRepository $repository, TokenGeneratorInterface $tokenGenerator)
	{
		$this->em = $em;
		$this->tokenGenerator = $tokenGenerator;
		$this->repository = $repository;
	}

	/**
	 * @param User $user
	 *
	 * @param bool $confirmation
	 *
	 * @return Token
	 */
	public function createToken(User $user, bool $confirmation = false): Token
	{
		if($token = $user->getToken()){
			$this->deleteToken($token);
			$this->em->flush();
		}

		$this->token = new Token();
		$this->token->setCreatedAt(new \DateTime())
			->setTokenCode($this->tokenGenerator->generateToken())
			->setUser($user);
		if($confirmation)
			$this->token->generateConfirmationCode(1111,9999);
		$user->setToken($this->token);

		return $this->token;
	}

	/**
	 * @param String $tokenCode
	 *
	 * @return User|null
	 */
	public function getUserFromToken(String $tokenCode): ?User
	{
		$user = null;

		if($token = $this->repository->findOneByTokenCode($tokenCode)){
			$this->token = $token;
			$user = $this->token->getUser();
		}

		return $user;

	}

	public function isTokenExpired()
	{
		if(!$this->token)   return true;
		$createdAt = $this->token->getCreatedAt();
		$createdAt->add(new \DateInterval('PT2H'));

		if($createdAt < new \DateTime()){
			return true;
		}
		return false;
	}

	public function deleteToken(Token $token = null)
	{
		if($token == null)
			$token = $this->token;
		$token->setUser(null);
		$this->em->remove($token);
	}
}