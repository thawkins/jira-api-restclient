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

namespace thawkins\Jira\Users;


use thawkins\Jira\Api\Authentication\AuthenticationInterface;
use thawkins\Jira\Api\Client\ClientInterface;
use thawkins\Jira\Api\Client\CurlClient;
use thawkins\Jira\Api\Exception;
use thawkins\Jira\Api\UnauthorizedException;

class Api
{
    const REQUEST_GET = 'GET';
    const REQUEST_POST = 'POST';
    const REQUEST_PUT = 'PUT';
    const REQUEST_DELETE = 'DELETE';

    const AUTOMAP_FIELDS = 0x01;

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
     * Create a JIRA API client.
     *
     * @param string $endpoint Endpoint URL.
     * @param AuthenticationInterface $authentication Authentication.
     * @param ClientInterface $client Client.
     */
    public function __construct(
        $endpoint,
        AuthenticationInterface $authentication,
        ClientInterface $client = null
    )
    {
        $this->setEndPoint($endpoint);
        $this->authentication = $authentication;

        if (is_null($client)) {
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

        if ($url !== $this->endpoint) {
            $this->endpoint = $url;
            $this->clearLocalCaches();
        }
    }


    /**
     * Get specified userts.
     *
     * @param $user_key
     * @param string $expand Expand.
     *
     * @return false|Result /Result|false
     * @throws Exception
     * @throws UnauthorizedException
     */
    public function getUsers($user_key, $expand = '')
    {
        return $this->api(self::REQUEST_GET, sprintf('/rest/api/latest/users/%s', $user_key), array('expand' => $expand));
    }


    /**
     * Query users.
     *
     * @param $username
     * @param integer $start_at Start at.
     * @param integer $max_results Max results.
     * @param string $fields Fields.
     *
     * @return Result
     * @throws Exception
     * @throws UnauthorizedException
     */
    public function search($username, $start_at = 0, $max_results = 1000, $fields = '*')
    {
        return $this->api(
            self::REQUEST_GET,
            '/rest/api/latest/users/search',
            array(
                'username' => $username,
                'startAt' => $start_at,
                'maxResults' => $max_results,
                'expand'=>'groups,applicationRoles,name'
            )
        );
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
     * @return Result|false
     * @throws Exception
     * @throws UnauthorizedException
     */
    public function api(
        $method = self::REQUEST_GET,
        $url = "",
        $data = array()
    )
    {
        $result = $this->client->sendRequest(
            $method,
            $url,
            $data,
            $this->getEndpoint(),
            $this->authentication,
            false,
            false
        );

        if (strlen($result)) {
            $json = json_decode($result, true);
            return new Result($json);
        } else {
            return false;
        }
    }


    /**
     * Automaps issue fields.
     *
     * @param array $user
     * @return array
     */
    protected function automapFields(array $user)
    {
        if (isset($user['fields'])) {
            $x = array();

            foreach ($user['fields'] as $kk => $vv) {
                if (isset($this->fields[$kk])) {
                    $x[$this->fields[$kk]['name']] = $vv;
                } else {
                    $x[$kk] = $vv;
                }
            }

            $user['fields'] = $x;
        }

        return $user;
    }

}
