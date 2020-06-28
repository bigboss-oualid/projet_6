<?php

namespace App\Tests\Entity;

use App\Entity\Trick;
use App\Entity\Video;
use PHPUnit\Framework\TestCase;

class VideoTest extends TestCase

{
	public function testAttributes()
	{
		$trickMock = $this->createMock(Trick::class);

		$video = new Video();
		$video->setEmbedCode('Iframe contenu');
		$video->setTrick($trickMock);

		static::assertNull($video->getId());
		static::assertEquals('Iframe contenu', $video->getEmbedCode());
		static::assertEquals(0, $video->getTrick()->getId());

		$video->setEmbedCode('edit Iframe contenu');

		static::assertEquals('edit Iframe contenu', $video->getEmbedCode());
	}
}
