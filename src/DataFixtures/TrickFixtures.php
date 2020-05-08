<?php

namespace App\DataFixtures;

use App\Entity\Avatar;
use App\Entity\Category;
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
	    $category1 = new Category();
	    $category2 = new Category();
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
		    ->setHash($this->encoder->encodePassword($adminUser, 'password'))
		    ->addUserRole($adminRole);


	    $avatar->setFilename('admin.png')
		    ->setOldName('admin.png')
		    ->setUser($adminUser);

	    $manager->persist($adminUser);
	    $manager->persist($avatar);


	    //create 10 users
	    $users = [];

	    for($i=1; $i <= 10; $i++){
		    $user = new User();
		    $bloggerAvatar = new Avatar();
		    $hash = $this->encoder->encodePassword($user, 'password');

		    $user->setAvatar($bloggerAvatar)
			    ->setLastName("l.name-$i")
			    ->setFirstName("f.name-$i")
			    ->setUsername("username-$i")
			    ->setEmail("username-$i@mail.de")
			    ->setHash($hash);

		    $bloggerAvatar->setFilename('defaultAvatar.png')
			    ->setOldName('defaultAvatar.png')
			    ->setUser($user);

		    $manager->persist($user);
		    $users[] = $user;
	    }


	    //Create Tricks
		for($i=1; $i <= 10; $i++){
			$trick = new Trick();
			if ($i&1){
				$trick->setCategory($category1)
					->setPublished(0);;
			}
			else {
				$trick->setCategory($category2)
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

			$manager->persist($trick);
		}
        $manager->flush();
    }
}
