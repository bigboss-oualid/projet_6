<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 22/06/2020
 * Time: 00:17
 */

namespace App\Tests\Listener;

use App\Entity\Illustration;
use App\Listener\FileUploaderSubscriber;
use App\Service\FileUploader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PostRemove;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreRemove;
use Doctrine\ORM\Mapping\PreUpdate;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class FileUploaderSubscriberTest extends TestCase
{
	public function testEventSubscription() {
		$fileUploaderSubscriber = new FileUploaderSubscriber(new FileUploader('targetDirectory'));
		$this->assertArrayHasKey(PrePersist::class, $fileUploaderSubscriber->getSubscribedEvents());
		$this->assertArrayHasKey(PreRemove::class, $fileUploaderSubscriber->getSubscribedEvents());
		$this->assertArrayHasKey(PreUpdate::class, $fileUploaderSubscriber->getSubscribedEvents());
		$this->assertArrayHasKey(PostRemove::class, $fileUploaderSubscriber->getSubscribedEvents());
	}

	public function testLoadImageByPrePersist(){
		$illustration = new Illustration();
		$imageFile = $this->getMockBuilder(File::class)
			->disableOriginalConstructor()
			->getMock();
		$illustration->setImageFile($imageFile);
		$fileUploader = $this->getMockBuilder(FileUploader::class)
			->disableOriginalConstructor()
			->getMock();
		$subscriber = new FileUploaderSubscriber($fileUploader);

		$em = $this->getMockBuilder(ObjectManager::class)
			->disableOriginalConstructor()
			->getMock();

		$lifecycleEventArgs = new LifecycleEventArgs($illustration, $em);

		$fileUploader->expects($this->once())->method('upload');
		$subscriber->prePersist($lifecycleEventArgs);

	}

}
