<?php
require __DIR__ . '/../vendor/autoload.php';

/**
 * @return thawkins\Jira\Api
 */
function getApiClient()
{
	$api = new \thawkins\Jira\Api(
		'https://your-jira-project.net',
		new \thawkins\Jira\Api\Authentication\Basic('yourname', 'password')
	);

	return $api;
}
