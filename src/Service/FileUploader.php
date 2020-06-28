<?php

namespace App\Service;

use App\Entity\Picture;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileUploader
{

	private $targetDirectory;

	public function __construct($targetDirectory)
	{
		$this->targetDirectory = $targetDirectory;
	}

	/**
	 * @param $entity
	 */
	public function upload(Picture $entity)
	{
		/**
		 * @var UploadedFile $file
		 */
		$file = $entity->getImageFile();
		$relativeDir = 'images/uploads/'.$this->getClassName($entity);
		$absoluteUploadDir = $this->targetDirectory.'/'.$relativeDir;

		$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$safeFilename = iconv('UTF-8', 'ASCII//TRANSLIT', $originalFilename);

		$fileName = $safeFilename . '_' . uniqid() . '.' . $file->guessClientExtension();

		try {
			$file->move($absoluteUploadDir, $fileName);
		} catch (FileException $e) {
			// ... handle exception if something happens during file upload
			throw new FileException('Failed to upload file:  ' . $e->getMessage());
		}

		$entity->setFilename($fileName);
		$entity->setPath($relativeDir . '/' . $fileName);
	}

	public function deletePicture(Picture $entity)
	{
		// PostRemove => We no longer have the entity's ID => Use the name we saved
		if(file_exists($entity->getTempFilename())) {
			// Remove file
			unlink($this->targetDirectory .'/'. $entity->getTempFilename());
		}
	}

	/**
	 * get short name of class to determinate upload's folder
	 * @param $object
	 *
	 * @return bool|string
	 */
	private function getClassName($object)
	{
		return strtolower(substr(strrchr(get_class($object), "\\"), 1));
	}

}