<?php
/*
 * The MIT License
 *
 * Copyright (c) 2014 Shuhei Tanuma
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace thawkins\Jira\AuthenticatedFetch;


use thawkins\Jira\Api\Authentication\AuthenticationInterface;
use thawkins\Jira\Api\Client\ClientInterface;
use thawkins\Jira\Api\Client\CurlClient;

class Api
{
    const REQUEST_GET = 'GET';
    const REQUEST_POST = 'POST';
    const REQUEST_PUT = 'PUT';
    const REQUEST_DELETE = 'DELETE';

    /**
     * Endpoint URL.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Client.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * Authentication.
     *
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * Options.
     *
     * @var integer
     */
    protected $options = 0;

    /**
     * Client-side cache of fields. List of fields when loaded, null when nothing is fetched yet.
     *
     * @var array|null
     */
    protected $fields;

    /**
     * Client-side cache of priorities. List of priorities when loaded, null when nothing is fetched yet.
     *
     * @var array|null
     */
    protected $priorities;

    /**
     * Client-side cache of statuses. List of statuses when loaded, null when nothing is fetched yet.
     *
     * @var array|null
     */
    protected $statuses;

    /**
     * Client-side cache of resolutions. List of resolutions when loaded, null when nothing is fetched yet.
     *
     * @var array|null
     */
    protected $resolutions;

    /**
     * Create a JIRA API client.
     *
     * @param string                  $endpoint       Endpoint URL.
     * @param AuthenticationInterface $authentication Authentication.
     * @param ClientInterface         $client         Client.
     */
    public function __construct(
        $endpoint,
        AuthenticationInterface $authentication,
        ClientInterface $client = null
    ) {
        $this->setEndPoint($endpoint);
        $this->authentication = $authentication;

        if ( is_null($client) ) {
            $client = new CurlClient();
        }

        $this->client = $client;
    }

    /**
     * Sets options.
     *
     * @param integer $options Options.
     *
     * @return void
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Get Endpoint URL.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set Endpoint URL.
     *
     * @param string $url Endpoint URL.
     *
     * @return void
     */
    public function setEndpoint($url)
    {
        // Remove trailing slash in the url.
        $url = rtrim($url, '/');

        if ( $url !== $this->endpoint ) {
            $this->endpoint = $url;
            $this->clearLocalCaches();
        }
    }

    /**
     * Helper method to clear the local caches. Is called when switching endpoints
     *
     * @return void
     */
    protected function clearLocalCaches()
    {
        $this->fields = null;
        $this->priorities = null;
        $this->statuses = null;
        $this->resolutions = null;
    }


    public function getHtml($url)
    {
        return $this->fetch(self::REQUEST_GET, $url, [], true);
    }


    /**
     * Send request to specified host.
     *
     * @param string $method Request method.
     * @param string $url URL.
     * @param array|string $data Data.
     * @param boolean $return_as_array Return results as associative array.
     * @param boolean $is_file Is file-related request.
     * @param boolean $debug Debug this request.
     *
     * @return mixed
     * @throws \thawkins\Jira\Api\Exception
     * @throws \thawkins\Jira\Api\UnauthorizedException
     */
    public function fetch(
        $method = self::REQUEST_GET,
        $url,
        $data = array(),
        $return_as_array = false,
        $is_file = false,
        $debug = false
    ) {
        $result = $this->client->sendRequest(
            $method,
            $url,
            $data,
            $this->getEndpoint(),
            $this->authentication,
            $is_file,
            $debug
        );

        if ( strlen($result) ) {
         return $result;
        }
        else {
            return false;
        }
    }
}
