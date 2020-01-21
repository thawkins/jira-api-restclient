<?php

namespace Tests\thawkins\Jira\Api\Client;


use thawkins\Jira\Api\Client\ClientInterface;
use thawkins\Jira\Api\Client\CurlClient;

class CurlClientTest extends AbstractClientTestCase
{

	/**
	 * Creates client.
	 *
	 * @return ClientInterface
	 */
	protected function createClient()
	{
		return new CurlClient();
	}

}
