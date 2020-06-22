<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 19/06/2020
 * Time: 19:48
 */

namespace App\Tests\Entity;

use App\DataFixtures\AppFixtures;
use App\Entity\Group;
use App\Entity\Illustration;
use App\Entity\User;
use App\Entity\UserUpdateTrick;
use App\Entity\Video;
use App\Entity\Trick;
use Doctrine\Common\Collections\ArrayCollection;
use  Doctrine\Common\EventArgs;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;


final class TrickTest extends KernelTestCase
{
	use FixturesTrait;

	private function assertHasErrors(Trick $trick, int $number = 0)
	{
		self::bootKernel();
		$errors = self::$container->get('validator')->validate($trick);
		$messages = [];
		/**@var ConstraintViolation */
		foreach($errors as $error) {
			$messages[] = $error->getPropertyPath(). ' => ' . $error->getMessage();
		}
		$this->assertCount($number, $errors, implode(', ', $messages));
	}

	public function testValidEntityTitle()
	{
		$this->assertHasErrors((new Trick)->setTitle('Titre de test'),0);
	}

	public function testInvalidBlankTitleEntity()
	{
		$this->assertHasErrors(new Trick,1);
	}

	public function testInvalidUsedTitle()
	{
		$this->loadFixtures([AppFixtures::class]);
		$this->assertHasErrors((new Trick())->setTitle("Mute"),1);
	}

	public function testCreateSlug()
	{
		$trickEntity = new Trick();
		$trickEntity->setTitle("&titre/not&&clean§");

		$eventArgs = $this->getMockBuilder(EventArgs::class)
			->disableOriginalConstructor()->getMock();
		$trickEntity->createSlug($eventArgs);
		$this->assertEquals("titre-notcleanss", $trickEntity->getSlug());
	}

	/**
	 * @param string       $title
	 * @param string       $description
	 * @param User         $userMock
	 * @param array        $illustrationsCollection
	 * @param array        $videosCollection
	 * @param \ArrayAccess $groupsCollection
	 *
	 * @dataProvider provideTrickData
	 */
	public function testCreateTrickWithGoodValues(string $title, string $description, User $userMock, array $illustrationsCollection, array $videosCollection, \ArrayAccess $groupsCollection)
	{
		$trick =  $this->getEntity($title, $description, $userMock, $illustrationsCollection, $videosCollection, $groupsCollection);

		static::assertInstanceOf(Trick::class, $trick);
		//static::assertIsString('string', $trick->getSlug());
		static::assertIsInt( $trick->getCreatedAt()->getTimestamp());
		static::assertSame($title, $trick->getTitle());
		static::assertSame($description, $trick->getDescription());
		static::assertSame($userMock, $trick->getAuthor());

		static::assertInstanceOf(\ArrayAccess::class, $trick->getIllustrations());
		foreach ($illustrationsCollection as $key => $illustration) {
			static::assertSame($illustration, $trick->getIllustrations()->get($key));
		}
		static::assertInstanceOf(\ArrayAccess::class, $trick->getVideos());
		foreach ($videosCollection as $key => $video) {
			static::assertSame($video, $trick->getVideos()->get($key));
		}
		static::assertInstanceOf(\ArrayAccess::class, $trick->getGroups());
		foreach ($groupsCollection as $key => $group) {
			static::assertSame($group, $trick->getGroups()->get($key));
		}
	}

	/**
	 * @param string $title
	 * @param string $description
	 * @param User   $userMock
	 *
	 * @dataProvider provideTrickData
	 */
	public function testUpdateTrickWithGoodValue(string $title, string $description, User $userMock)
	{
		$trick =  (new Trick())->setTitle($title)
			->setDescription($description)
			->setAuthor($userMock);
		$userMockUpdateTrick = $this->createMock(User::class);

		$update = new UserUpdateTrick($userMockUpdateTrick, $trick);

		// Update Trick
		$newTitle = 'NewTitle';
		$newDescription = 'NewDescription';
		$newIllustrationsCollection = [
			$this->createMock(Illustration::class),
			$this->createMock(Illustration::class),
			$this->createMock(Illustration::class),
			$this->createMock(Illustration::class)
		];
		$newVideosCollection = [
			$this->createMock(Video::class),
			$this->createMock(Video::class)
		];
		$newGroupsCollection = new ArrayCollection();
		$newGroupsCollection->add($this->createMock(Group::class));

		$trick->setTitle($newTitle)
			->setDescription($newDescription)
			->addUpdatedBy($update);

		foreach($newIllustrationsCollection as $illustration){
			$trick->addIllustration($illustration);
		}
		foreach($newVideosCollection as $video){
			$trick->addVideo($video);
		}
		foreach($newGroupsCollection as $group){
			$trick->addGroup($group);
		}

		static::assertSame($newTitle, $trick->getTitle());
		static::assertSame($newDescription, $trick->getDescription());
		static::assertInstanceOf( \ArrayAccess::class, $trick->getUpdatedBy());
		static::assertSame($update, $trick->getUpdatedBy()[0]);
		static::assertInstanceOf(\ArrayAccess::class, $trick->getIllustrations());
		foreach ($newIllustrationsCollection as $key => $newIllustration) {
			static::assertSame($newIllustration, $trick->getIllustrations()->get($key));
		}
		static::assertInstanceOf(\ArrayAccess::class, $trick->getVideos());
		foreach ($newVideosCollection as $key => $video) {
			static::assertSame($video, $trick->getVideos()->get($key));
		}
		static::assertInstanceOf(\ArrayAccess::class, $trick->getGroups());
		foreach ($newGroupsCollection as $key => $group) {
			static::assertSame($group, $trick->getGroups()->get($key));
		}
	}

	private function getEntity(string $title, string $description, User $userMock, array $illustrationsCollection, array $videosCollection, \ArrayAccess $groupsCollection): Trick
	{
		$trick = (new Trick())->setTitle($title)
			->setDescription($description)
			->setAuthor($userMock);
		foreach($illustrationsCollection as $illustration){
			$trick->addIllustration($illustration);
		}
		foreach($videosCollection as $video){
			$trick->addVideo($video);
		}
		foreach($groupsCollection as $group){
			$trick->addGroup($group);
		}
		return $trick;
	}

	private function provideTrickData()
	{
		$title = 'Title';
		$description = 'Description';
		$userMock = $this->createMock(User::class);
		$illustrationsCollection = [
			$this->createMock(Illustration::class),
			$this->createMock(Illustration::class),
			$this->createMock(Illustration::class),
		];
		$videosCollection = [
			$this->createMock(Video::class),
			$this->createMock(Video::class),
			$this->createMock(Video::class),
		];

		$groupsCollection = new ArrayCollection();
		$groupsCollection->add($this->createMock(Group::class));
		$groupsCollection->add($this->createMock(Group::class));

		yield [
			$title,
			$description,
			$userMock,
			$illustrationsCollection,
			$videosCollection,
			$groupsCollection,
		];
	}
}