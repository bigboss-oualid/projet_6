<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 23/06/2020
 * Time: 22:21
 */

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
	use FixturesTrait;

	public function testDisplayLogin()
	{
		$client = static::createClient();
		$client->request('GET', '/login');
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorTextContains('h3', 'CONNEXION');
		$this->assertSelectorNotExists('.alert.alert-danger');

	}

	public function testLoginWithBadCredentials()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/login');
		$form = $crawler->selectButton('Connexion!')->form([
			'_username' => 'bigboss',
			'_password' => 'fakepassword'
		]);
		$client->submit($form);
		$this->assertResponseRedirects('/login');
		$client->followRedirect();
		$this->assertSelectorExists('.alert.alert-danger');
	}

	public function testSuccessFullLoginWithForm()
	{
		$client = static::createClient();
		$this->loadFixtureFiles([dirname(__DIR__). '/fixtures/Users.yaml']);
		$crawler = $client->request('GET', '/login');
		$form = $crawler->selectButton('Connexion!')->form([
			'_username' => 'test',
			'_password' => 'test'
		]);
		$client->submit($form);
		$this->assertResponseRedirects('/tricks/page/1');
	}

	public function testSuccessFullLoginWithToken ()
	{
		$client = static::createClient();
		$this->loadFixtureFiles([dirname(__DIR__). '/fixtures/Users.yaml']);
		$csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
		$client->request('POST', '/login', [
			'_csrf_token' => $csrfToken,
			'_username' => 'test',
			'_password' => 'test'
		]);
		$this->assertResponseRedirects('/tricks/page/1');
	}

}
