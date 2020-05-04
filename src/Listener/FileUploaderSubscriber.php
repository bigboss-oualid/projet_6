<?php

namespace App\Listener;

use App\Entity\Picture;
use App\Service\FileUploader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class FileUploaderSubscriber implements EventSubscriber
{
	/**
	 * @var FileUploader
	 */
	private $uploader;
	private $targetDirectory;

	/**
	 * FileUploaderSubscriber constructor.
	 *
	 * @param FileUploader $uploader
	 * @param              $targetDirectory
	 */
	public function __construct(FileUploader $uploader, $targetDirectory)
	{
		$this->uploader = $uploader;
		$this->targetDirectory = $targetDirectory;
	}

	public function getSubscribedEvents()
	{
		return [
			'prePersist',
			'preUpdate',
			'preRemove',
			'postRemove'
		];
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function prePersist(LifecycleEventArgs $args) {

		$entity = $args->getEntity();

		if (!$entity instanceof Picture) {
			return;
		}

		$illustrationFileName = $this->uploader->upload($entity->getImageFile());
		$entity->setFilename($illustrationFileName);
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function preUpdate(LifecycleEventArgs $args) {

		$entity = $args->getEntity();

		if (!$entity instanceof Picture) {
			return;
		}
		unlink($this->targetDirectory .'/' .$entity->getFilename());
		$illustrationFileName = $this->uploader->upload($entity->getImageFile());

		$entity->setFilename($illustrationFileName);
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function preRemove(LifecycleEventArgs $args) {

		$entity = $args->getEntity();
		if (!$entity instanceof Picture) {
			return;
		}
		$entity->setTempFilename($this->targetDirectory .'/' . $entity->getFilename());
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function postRemove(LifecycleEventArgs $args) {
		$entity = $args->getEntity();
		if (!$entity instanceof Picture) {
			return;
		}
		// PostRemove => We no longer have the entity's ID => Use the name we saved
		if (file_exists($entity->getTempFilename()))
		{
			// Remove file
			unlink($entity->getTempFilename());
		}
	}
}