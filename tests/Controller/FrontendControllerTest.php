<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 22/06/2020
 * Time: 02:10
 */

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class FrontendControllerTest extends WebTestCase
{

	public function testHomePage()
	{
		$client = static::createClient();
		$client->request('GET', '/');
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
	}

	public function testH3TricksPage()
	{
		$client = static ::createClient();
		$client->request('GET', '/tricks/page/1');
		$this->assertSelectorTextContains('h3', 'TOUS NOS FIGURES');
	}
}
