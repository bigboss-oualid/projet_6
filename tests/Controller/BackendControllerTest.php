<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 23/06/2020
 * Time: 21:24
 */

namespace App\Tests\Controller;

use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BackendControllerTest extends WebTestCase
{

	use FixturesTrait;

	public function testCreateTrickPageIsRestricted()
	{
		$client = static::createClient();
		$client->request('GET', '/tricks/new');
		$this->assertResponseRedirects('http://localhost/login', 302);
	}

	public function testRedirectToLogin()
	{
		$client = static::createClient();
		$client->request('GET', '/tricks/new');
		$this->assertTrue($client->getResponse()->isRedirect("http://localhost/login"));
	}

	public function testLetAuthenticatedUserAccess()
	{
		$client = static::createClient();
		$users = $this->loadFixtureFiles([dirname(__DIR__). '/fixtures/Users.yaml']);
		/**@var User $user*/
		$user = $users['user_user'];
		$this->login($client, $user);
		$client->request('GET', '/tricks/new');
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
	}

	public function testDeleteTrickPageIsRestricted()
	{
		$client = static::createClient();
		$client->request('GET', '/tricks/1/delete');
		$this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
	}

	private function login(kernelBrowser $client, User $user){

		$session = $client->getContainer()->get('session');
		$token = new UsernamePasswordToken($user, null,'main',['ROLE_USER']);
		$session->set('_security_main', serialize($token));
		$session->save();
		$cookie = new Cookie($session->getName(), $session->getId());
		$client->getCookieJar()->set($cookie);
	}
}
