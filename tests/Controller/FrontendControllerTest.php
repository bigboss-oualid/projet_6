<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 22/06/2020
 * Time: 02:10
 */

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class FrontendControllerTest extends WebTestCase

{
	protected static function getKernelClass()
	{
		return \App\Kernel::class;
	}

	public function testHome()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/');
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}

	public function testCorrectShowPage()
	{
		$client = static::createClient();
		$value = 1;
		$slug = "mute";
		$url = "/tricks/$slug/$value";
		$crawler = $client->request('GET', $url);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}


	public function testIncorrectTricksPage()
	{
		$client = static::createClient();
		$page = 0;
		$url = "/tricks/page/$page";
		$crawler = $client->request('GET', $url);
		$this->assertEquals(500, $client->getResponse()->getStatusCode());
	}

}
