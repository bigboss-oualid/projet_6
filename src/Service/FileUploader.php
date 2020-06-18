<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileUploader
{
	private $targetDirectory;

	public function __construct(string $targetDirectory)
	{
		$this->targetDirectory = $targetDirectory;
	}

	/**
	 * @param UploadedFile $file
	 *
	 * @return string
	 */
	public function upload(UploadedFile $file): string
	{
		$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$safeFilename = iconv('UTF-8', 'ASCII//TRANSLIT', $originalFilename);

		$fileName = $safeFilename . '_' . uniqid() . '.' . $file->guessClientExtension();

		try {
			$file->move($this->getTargetDirectory(), $fileName);
		} catch (FileException $e) {
			// ... handle exception if something happens during file upload
			throw new FileException('Failed to upload file:  ' . $e->getMessage());
		}

		return $fileName;
	}

	public function getTargetDirectory()
	{
		return $this->targetDirectory;
	}

}