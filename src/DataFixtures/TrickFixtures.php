<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
	    $category1 = new Category();
	    $category2 = new Category();
	    $category1->setName("mute");
	    $category2->setName("sad");

		for($i=1; $i <= 10; $i++){
			$trick = new Trick();
			if ($i&1){
				$trick->setCategory($category1);
			}
			else {
				$trick->setCategory($category2);
			}

			$trick->setTitle("titre n$i");
			$trick->setDescription("Description du Trick n°$i");
			$trick->createSlug();
			$trick->setPublished(1);
			$manager->persist($trick);
		}
        $manager->flush();
    }
}
