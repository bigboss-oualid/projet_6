<?php

namespace App\DataFixtures;

use App\Entity\Avatar;
use App\Entity\Comment;
use App\Entity\Group;
use App\Entity\Illustration;
use App\Entity\Rating;
use App\Entity\Role;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
final class AppFixtures extends Fixture
{
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $encoder;

	/**
	 * AppFixtures constructor.
	 *
	 * @param UserPasswordEncoderInterface $encoder
	 */
	public function __construct(UserPasswordEncoderInterface $encoder)
	{
		$this->encoder = $encoder;
	}

	/**
	 * @param string $entityName
	 *
	 * @return array
	 */
	private function getDataFixture(string $entityName) :array
	{
		return Yaml::parse(file_get_contents(__DIR__.'/Fixtures/'. $entityName .'.yaml', true));
	}

	/**
	 * {@inheritdoc}
	 */
	public function load(ObjectManager $manager)
	{
		$illustrations = $this->getDataFixture('Illustration');
		$avatars       = $this->getDataFixture('Avatar');
		$users         = $this->getDataFixture('User');
		$tricks        = $this->getDataFixture('Trick');
		$videos        = $this->getDataFixture('Video');
		$comments      = $this->getDataFixture('Comment');
		$groups        = $this->getDataFixture('Group');
		//Create Groups
		foreach ($groups as $name => $group) {
			$groupEntity = new Group();
			$groupEntity->setName($group['Name']);

			$this->addReference($name, $groupEntity);
			$manager->persist($groupEntity);
		}

		//Create admin role
		$adminRole = new Role();
		$adminRole->setTitle('ROLE_ADMIN');
		$manager->persist($adminRole);

		//Create users
		foreach ($users as $name => $user) {
			$avatar = new Avatar();
			$userEntity = new User();

			$avatar->setFilename($avatars[$name]['filename'])
				->setPath($avatars[$name]['path'])
				->setUser($userEntity);
			$userEntity->setUsername($user['Username'])
				->setLastName($user['Lastname'])
				->setFirstName($user['Firstname'])
				->setEmail($user['Email'])
				->setHash($this->encoder->encodePassword($userEntity, $user['Password']))
				->setAvatar($avatar)
				->setEnabled(True);
			if($user['Firstname'] === 'Admin')
				$userEntity->addUserRole($adminRole);

			$manager->persist($avatar);
			$manager->persist($userEntity);
			$this->addReference($name, $userEntity);
		}

		//Set references entries to managed $objects (ilustrations & videos)
		foreach ($illustrations as $name => $illustration) {
			$illustrationEntity = new Illustration();
			$illustrationEntity->setFilename($illustration['filename'])
				->setPath($illustration['path']);

			$this->addReference($name, $illustrationEntity);
			$manager->persist($illustrationEntity);
		}
		foreach ($videos as $name => $video) {
			$videoEntity = new Video();
			$videoEntity->setEmbedCode($video['iFrame']);

			$this->addReference($name, $videoEntity);
			$manager->persist($videoEntity);
		}

		//Create Tricks
		foreach ($tricks as $name => $trick) {
			/**@var User $author */
			$author = $this->getReference($trick['Reference']['Author']);
			$trickEntity = new Trick();

			$tricksIllustrations = $trick['Reference']['Illustrations'];
			if($tricksIllustrations){
				foreach ($tricksIllustrations as $illustrationReference) {
					$trickEntity->addIllustration($this->getReference($illustrationReference));
				}
			}

			$tricksVideos = $trick['Reference']['Videos'];
			if($tricksVideos){
				foreach ($tricksVideos as $videoReference) {
					$trickEntity->addVideo($this->getReference($videoReference));
				}
			}

			foreach ($trick['Reference']['Groups'] as $groupReference) {
				$trickEntity->addGroup($this->getReference($groupReference));
			}

			$trickEntity->setTitle($trick['Title'])
				->setDescription($trick['Description'])
				->setAuthor($author);

			$rating = new Rating();
			$rating->setRating(rand(1,5))
				->setAuthor($author)
				->setTrick($trickEntity);

			$manager->persist($trickEntity);
			$manager->persist($rating);

			$this->addReference($name, $trickEntity);
		}

		//Create Comments
		foreach ($comments as $comment) {
			/**@var Trick $trick*/
			$trick = $this->getReference($comment['Reference']['Trick']);
			$author = $this->getReference($comment['Reference']['Author']);

			$commentEntity = new Comment();
			$commentEntity->setContent($comment['Content'])
				->setAuthor($author)
				->setTrick($trick);

			$manager->persist($commentEntity);
		}

		$manager->flush();
		echo "\n Loading fixtures is terminated!\n";
	}
}
