<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 21/06/2020
 * Time: 15:09
 */

namespace App\Tests;

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends kernelTestCase
{
	use FixturesTrait;

	public function testCount(){
		self::bootKernel();
		$users = $this->loadFixtureFiles([
			dirname(__DIR__). '/fixtures/UserTestRepositoryFixtures.yaml'
		]);
		$usersNumber = self::$container->get(UserRepository::class)->count([]);
		$this->assertIsInt($users['user1']->getId());
		$this->assertEquals(10, $usersNumber);
	}
}
