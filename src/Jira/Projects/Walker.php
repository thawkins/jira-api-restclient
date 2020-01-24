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
namespace thawkins\Jira\Projects;


use Exception;
use thawkins\Jira\Api\UnauthorizedException;
use thawkins\Jira\Projects\Api;
use thawkins\Jira\Projects\Entity;


class Walker implements \Iterator, \Countable
{

    /**
     * API.
     *
     * @var Api
     */
    protected $api;

    /**
     * Query.
     *
     * @var string
     */
    protected $query = null;

    /**
     * Offset.
     *
     * @var integer
     */
    protected $offset = 0;

    /**
     * Current record index.
     *
     * @var integer
     */
    protected $current = 0;

    /**
     * Total user count.
     *
     * @var integer
     */
    protected $total = null;

    /**
     * Issue count on current page.
     *
     * @var integer
     */
    protected $max = 0;

    /**
     * Index of user in user list (across all user pages).
     *
     * @var integer
     */
    protected $startAt = 0;

    /**
     * Users per page.
     *
     * @var integer
     */
    protected $perPage = 1000000;

    /**
     * Was query executed.
     *
     * @var boolean
     */
    protected $executed = false;

    /**
     * Result.
     *
     * @var array
     */
    protected $entities = array();

    /**
     * List of fields to query.
     *
     * @var string|array|null
     */
    protected $fields = null;

    /**
     * Callback.
     *
     * @var callable
     */
    protected $callback;

    /**
     * Creates walker instance.
     *
     * @param Api          $api      API.
     * @param integer|null $per_page Per page.
     */
    public function __construct(Api $api, $per_page = null)
    {
        $this->api = $api;

        if ( is_numeric($per_page) ) {
            $this->perPage = $per_page;
        }
    }

    /**
     * Pushes query.
     *
     * @param string            $query    JQL.
     * @param string|array|null $fields Fields.
     *
     * @return void
     */
    public function push($query, $fields = null)
    {
        $this->query = $query;
        $this->fields = $fields;
    }

    /**
     * Return the current element.
     *
     * @return mixed Can return any type.
     * @link   http://php.net/manual/en/iterator.current.php
     */
    public function current()
    {
        if ( is_callable($this->callback) ) {
            $tmp = $this->entities[$this->offset];
            $callback = $this->callback;

            return $callback($tmp);
        }
        else {
            return $this->entities[$this->offset];
        }
    }

    /**
     * Move forward to next element.
     *
     * @return void Any returned value is ignored.
     * @link   http://php.net/manual/en/iterator.next.php
     */
    public function next()
    {
        $this->offset++;
    }

    /**
     * Return the key of the current element.
     *
     * @return mixed scalar on success, or null on failure.
     * @link   http://php.net/manual/en/iterator.key.php
     */
    public function key()
    {
        if ( $this->startAt > 0 ) {
            return $this->offset + (($this->startAt - 1) * $this->perPage);
        }
        else {
            return 0;
        }
    }

    /**
     * Checks if current position is valid.
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     *                 Returns true on success or false on failure.
     * @throws Exception When "Walker::push" method wasn't called.
     * @throws UnauthorizedException When it happens.
     * @link   http://php.net/manual/en/iterator.valid.php
     */
    public function valid()
    {
        if ( is_null($this->query) ) {
            throw new Exception('you have to call Jira_Walker::push($query, $fields) at first');
        }

        if ( !$this->executed ) {
            try {
                $result = $this->api->search($this->getQuery(),0,  $this->perPage);

                $this->setResult($result);
                $this->executed = true;

                if ( $result->getTotal() == 0 ) {
                    return false;
                }

                return true;
            }
            catch ( UnauthorizedException $e ) {
                throw $e;
            }
            catch ( Exception $e ) {
                error_log($e->getMessage());

                return false;
            }
        }
        else {
            if ( $this->offset >= $this->max && $this->key() < $this->total ) {
                try {
                    $result = $this->api->search($this->getQuery(), 0, $this->perPage);
                    $this->setResult($result);

                    return true;
                }
                catch ( UnauthorizedException $e ) {
                    throw $e;
                }
                catch ( Exception $e ) {
                    error_log($e->getMessage());

                    return false;
                }
            }
            else {
                if ( ($this->startAt - 1) * $this->perPage + $this->offset < $this->total ) {
                    return true;
                }
                else {
                    return false;
                }
            }
        }
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @return void Any returned value is ignored.
     * @link   http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind()
    {
        $this->offset = 0;
        $this->startAt = 0;
        $this->current = 0;
        $this->max = 0;
        $this->total = null;
        $this->executed = false;
        $this->entities = array();
    }

    /**
     * Count elements of an object.
     *
     * @return integer The custom count as an integer.
     * @throws Exception
     * @link   http://php.net/manual/en/countable.count.php
     */
    public function count()
    {
        if ( $this->total === null ) {
            $this->valid();
        }

        return $this->total;
    }

    /**
     * Sets callable.
     *
     * @param callable $callable Callable.
     *
     * @return void
     * @throws Exception When not a callable passed.
     */
    public function setDelegate($callable)
    {
        if ( is_callable($callable) ) {
            $this->callback = $callable;
        }
        else {
            throw new Exception('passed argument is not callable');
        }
    }

    /**
     * Sets result.
     *
     * @param Result $result Result.
     *
     * @return void
     */
    protected function setResult(Result $result)
    {
        $this->total = $result->getTotal();
        $this->offset = 0;
        $this->max = $result->getCount();
        $this->entities = $result->getEntities();
        $this->startAt++;
    }

    /**
     * Returns queryL.
     *
     * @return string
     */
    protected function getQuery()
    {
        return $this->query;
    }

}
