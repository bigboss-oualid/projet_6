<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase

{

	public function testAttributes()
	{
		$now = new \DateTime('NOW');
		$trickMock = $this->createMock(Trick::class);
		$userMock = $this->createMock(User::class);

		$comment = new Comment();
		$comment->setCreatedAt($now);
		$comment->setContent('comment');
		$comment->setAuthor($userMock);
		$comment->setTrick($trickMock);

		static::assertNull($comment->getId());
		static::assertEquals($now, $comment->getCreatedAt());
		static::assertEquals('comment', $comment->getContent());
		static::assertEquals(0, $comment->getAuthor()->getId());
		static::assertEquals(0, $comment->getTrick()->getId());
	}
}