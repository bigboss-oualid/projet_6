<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class PagesStatusTest extends WebTestCase
{
	/**
	 * @param $url
	 * @dataProvider urlProvider
	 */
	public function testNonLoggedUserPage($url)
	{
		$client = self::createClient();
		$client->request(Request::METHOD_GET, $url);

		static::assertTrue($client->getResponse()->isSuccessful());

	}

	/**
	 * @param $url
	 * @dataProvider protectedUrlProvider
	 */
	public function testLoggedUserPage($url)
	{
		$client = self::createClient();
		$client->request(Request::METHOD_GET, $url);

		static::assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
	}

	/**
	 * @return \Generator
	 */
	public function urlProvider()
	{
		return [
			['/'],
			['/login'],
		    ['/register'],
		    ['/forgotten-password'],
		    ['/tricks/page/1']
		];
	}

	/**
	 * @return \Generator
	 */
	public function protectedUrlProvider()
	{
		yield ['/account/page/1'];
		yield ['/account/password-update'];
		yield ['/tricks/new'];
	}
}