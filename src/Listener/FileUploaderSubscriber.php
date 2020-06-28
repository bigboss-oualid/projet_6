<?php

namespace App\Listener;

use App\Entity\Picture;
use App\Service\FileUploader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PostRemove;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreRemove;
use Doctrine\ORM\Mapping\PreUpdate;

class FileUploaderSubscriber implements EventSubscriber
{
	/**
	 * @var FileUploader
	 */
	private $uploader;

	/**
	 * FileUploaderSubscriber constructor.
	 *
	 * @param FileUploader $uploader
	 */
	public function __construct(FileUploader $uploader)
	{
		$this->uploader = $uploader;
	}

	public function getSubscribedEvents()
	{
		return [
			PrePersist::class => 'prePersist',
			PreUpdate::class  => 'preUpdate',
			PreRemove::class  => 'preRemove',
			PostRemove::class => 'postRemove'
		];
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function prePersist(LifecycleEventArgs $args)
	{

		$entity = $args->getEntity();
		if(!$entity instanceof Picture || $entity->getImageFile() == null) {
			return;
		}

		$this->uploader->upload($entity);
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function preUpdate(LifecycleEventArgs $args)
	{

		$entity = $args->getEntity();

		if(!$entity instanceof Picture || $entity->getImageFile() == null) {
			return;
		}
		$this->uploader->deletePicture($entity);
		$this->uploader->upload($entity);
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function preRemove(LifecycleEventArgs $args)
	{

		$entity = $args->getEntity();
		if(!$entity instanceof Picture) {
			return;
		}
		$entity->setTempFilename($entity->getPath());
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function postRemove(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if(!$entity instanceof Picture) {
			return;
		}

		$this->uploader->deletePicture($entity);
	}
}