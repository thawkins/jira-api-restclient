<?php

namespace Tests\thawkins\Jira\Api\Client;


use thawkins\Jira\Api\Client\ClientInterface;
use thawkins\Jira\Api\Client\PHPClient;

class PHPClientTest extends AbstractClientTestCase
{

	/**
	 * Creates client.
	 *
	 * @return ClientInterface
	 */
	protected function createClient()
	{
		return new PHPClient();
	}

}
