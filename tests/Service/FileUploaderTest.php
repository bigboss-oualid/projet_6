<?php

namespace App\Tests\Service;

use App\Entity\Avatar;
use App\Service\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderTest extends TestCase
{
	public function testUpload() {
		$fileUploader = new FileUploader('');
		$path = 'test/test.png';
		$image = new UploadedFile(
			$path,
			'test.png',
			'image/png',
			null,
			true
		);

		$testAvatar = (new Avatar)->setImageFile($image);

		$this->assertEquals($testAvatar->getFilename(), null);
		$fileUploader->upload($testAvatar);

		$this->assertNotNull($testAvatar->getFilename());
	}
}
