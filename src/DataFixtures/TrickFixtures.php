<?php



namespace App\DataFixtures;



use App\Entity\Avatar;

use App\Entity\Comment;

use App\Entity\Group;
use App\Entity\Role;

use App\Entity\Trick;

use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;

use Doctrine\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class TrickFixtures extends Fixture

{



	/**

	 * @var UserPasswordEncoderInterface

	 */

	private $encoder;



	public function __construct(UserPasswordEncoderInterface $encoder)

	{

		$this->encoder = $encoder;

	}



	public function load(ObjectManager $manager)

	{

		//Create 2 Categories

		$category1 = new Group();

		$category2 = new Group();

		$category1->setName("mute");

		$category2->setName("sad");



		//Create admin role

		$adminRole = new Role();

		$adminRole->setTitle('ROLE_ADMIN');

		$manager->persist($adminRole);



		//Create avatar

		$avatar = new Avatar();



		//Create admin user

		$adminUser = new User();

		$adminUser->setFirstName('walid')

			->setAvatar($avatar)

			->setLastName('bigboss')

			->setUsername('bigboss')

			->setEmail('admin@snowtricks.com')

			->setHash($this->encoder->encodePassword($adminUser, 'admin'))

			->addUserRole($adminRole)

			->setEnabled(True);





		$avatar->setFilename('admin.png')

			->setPath('admin.png')

			->setUser($adminUser);



		$manager->persist($adminUser);

		$manager->persist($avatar);





		//create 10 users

		$users = [];



		for($i=1; $i <= 10; $i++){

			$user = new User();

			$bloggerAvatar = new Avatar();

			$hash = $this->encoder->encodePassword($user, '123');



			$user->setAvatar($bloggerAvatar)

				->setLastName("l.name-$i")

				->setFirstName("f.name-$i")

				->setUsername("username-$i")

				->setEmail("username-$i@mail.de")

				->setHash($hash)

				->setEnabled(True);



			$bloggerAvatar->setFilename('defaultAvatar.png')

				->setPath('defaultAvatar.png')

				->setUser($user);



			$manager->persist($user);

			$users[] = $user;

		}





		//Create Tricks

		for($i=1; $i <= 10; $i++){

			$trick = new Trick();

			if ($i&1){

				$trick->addGroup($category1)

					->setPublished(0);;

			}

			else {

				$trick->addGroup($category2)

					->setPublished(1);;

			}



			$user = $users[mt_rand(0,count($users) - 1)];



			$trick->setTitle("titre n$i")

				->setDescription("Description du Trick n°$i");

			if($i<4){

				$trick->setAuthor($adminUser);

			} else {

				$trick->setAuthor($user);

			}

			//Create Comments

			if(mt_rand(0,1)) {

				$comment = new Comment();

				$comment->setContent("un commentaire de test")

					->setTrick($trick)

					->setAuthor($user);



				$manager->persist($comment);

			}



			$manager->persist($trick);

		}

		$manager->flush();

	}

}