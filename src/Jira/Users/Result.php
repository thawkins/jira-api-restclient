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


use thawkins\Jira\User;

class Result
{

	/**
	 * Expand.
	 *
	 * @var array
	 */
	protected $expand;

	/**
	 * Start at.
	 *
	 * @var integer
	 */
	protected $startAt;

	/**
	 * Max results.
	 *
	 * @var integer
	 */
	protected $maxResults;

	/**
	 * Total
	 *
	 * @var integer
	 */
	protected $total;

	/**
	 * Result.
	 *
	 * @var array
	 */
	protected $result;

	/**
	 * Creates result instance.
	 *
	 * @param array $result Result.
	 */
	public function __construct(array $result)
	{
		$this->total = count($result);
		$this->result = $result;
	}

	/**
	 * Returns total number of records.
	 *
	 * @return integer
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * Returns user count.
	 *
	 * @return integer
	 */
	public function getCount()
	{
		return count($this->getUsers());
	}

	/**
	 * Returns users.
	 *
	 * @return array
	 */
	public function getUsers()
	{
		if ( isset($this->result) ) {
			$result = array();

			foreach ( $this->result as $user ) {
				$result[] = new User($user);
			}

			return $result;
		}

		return array();
	}

	/**
	 * Returns raw result.
	 *
	 * @return array
	 */
	public function getResult()
	{
		return $this->result;
	}

}
