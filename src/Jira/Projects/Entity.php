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


use thawkins\Jira\Api\Exception;
use thawkins\Jira\Api\UnauthorizedException;
use thawkins\Jira\Projects\Api as ProjectApi;

class Entity
{
    /**
     * Creates user instance.
     *
     * @param array $user User.
     */
    public function __construct(array $user = array())
    {
        foreach ($user as $key => $value) {
            $this->$key = $value;
        }
    }


    /**
     * Gets jira's internal user id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->projectId;
    }

    /**
     * Gets jira's members for a given group name.
     *
     * @param ProjectApi $api
     * @return string
     * @throws Exception
     * @throws UnauthorizedException
     */
    public function getMembers(ProjectApi $api)
    {
        return $this->projectId;
    }

}
