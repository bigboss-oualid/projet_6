<?php

namespace App\DataFixtures;

use App\Entity\Category;
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
    	//mange Categories
	    $category1 = new Category();
	    $category2 = new Category();
	    $category1->setName("mute");
	    $category2->setName("sad");

	    //manage User
	    $users = [];

	    for($i=1; $i <= 10; $i++){
		    $user = new User();
		    $hash = $this->encoder->encodePassword($user, '-123Password');

		    $user->setLastName("l.name-$i");
		    $user->setFirstName("f.name-$i");
		    $user->setUsername("username-$i");
		    $user->setEmail("username-$i@mail.de");
		    $user->setHash($hash);
		    $user->setCreatedAt(new \DateTime());
		    $manager->persist($user);
		    $users[] = $user;
	    }


	    //manage Tricks
		for($i=1; $i <= 10; $i++){
			$trick = new Trick();
			if ($i&1){
				$trick->setCategory($category1);
			}
			else {
				$trick->setCategory($category2);
			}

			$user = $users[mt_rand(0,count($users) - 1)];

			$trick->setTitle("titre n$i");
			$trick->setDescription("Description du Trick n°$i");
			//$trick->createSlug();
			$trick->setPublished(1);
			$trick->setAuthor($user);

			$manager->persist($trick);
		}
        $manager->flush();
    }
}
