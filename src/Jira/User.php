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
namespace thawkins\Jira;


class User
{
    /**
     * ID.
     *
     * @var string
     */
    protected $id;

    /**
     * ID.
     *
     * @var string
     */
    protected $displayName;

    /**
     * Fields.
     *
     * @var array
     */
    protected $fields;

    /**
     * Expand information.
     *
     * @var array
     */
    protected $expandedInformation;

    /**
     * Creates user instance.
     *
     * @param array $user User.
     */
    public function __construct(array $user = array())
    {
       foreach($user as $key=>$value){
           $this->$key = $value;
       }
    }

    /**
     * Gets user key (YOURPROJ-123).
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Gets jira's internal user id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get this user api url.
     *
     * @return string
     */
    public function getSelf()
    {
        return $this->self;
    }

    /**
     * Get current fields.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get user summary.
     *
     * @return mixed
     */
    public function getSummary()
    {
        return $this->get('Summary');
    }

    /**
     * Get user type.
     *
     * @return mixed
     */
    public function getIssueType()
    {
        return $this->get('Issue Type');
    }

    /**
     * Get user reporter.
     *
     * @return mixed
     */
    public function getReporter()
    {
        return $this->get('Reporter');
    }

    /**
     * Get user created time.
     *
     * @return mixed
     */
    public function getCreated()
    {
        return $this->get('Created');
    }

    /**
     * Get the current assignee.
     *
     * @return mixed
     */
    public function getAssignee()
    {
        return $this->get('Assignee');
    }

    /**
     * Get user updated time.
     *
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->get('Updated');
    }

    /**
     * Get priority.
     *
     * @return mixed
     */
    public function getPriority()
    {
        return $this->get('Priority');
    }

    /**
     * Get description.
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->get('Description');
    }

    /**
     * Get user status.
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->get('Status');
    }

    /**
     * Get labels.
     *
     * @return mixed
     */
    public function getLabels()
    {
        return $this->get('Labels');
    }

    /**
     * Get project info.
     *
     * @return mixed
     */
    public function getProject()
    {
        return $this->get('Project');
    }

    /**
     * Get fix versions.
     *
     * @return mixed
     */
    public function getFixVersions()
    {
        return $this->get('Fix Version/s');
    }

    /**
     * Get resolutions.
     *
     * @return mixed
     */
    public function getResolution()
    {
        return $this->get('Resolution');
    }

    /**
     * Get resolution date.
     *
     * @return mixed
     * @todo   Is the field exists? Maybe there should be 'Planned End'?
     */
    public function getResolutionDate()
    {
        return $this->get('Resolutiondate');
    }

    /**
     * Get watches.
     *
     * @return mixed
     */
    public function getWatchers()
    {
        return $this->get('Watchers');
    }

    /**
     * Get due date.
     *
     * @return mixed
     */
    public function getDueDate()
    {
        return $this->get('Due Date');
    }

    /**
     * Get information represented in call output due to expand=... suffix.
     *
     * @return array
     * @see    https://docs.atlassian.com/jira/REST/latest/
     */
    public function getExpandedInformation()
    {
        return $this->expandedInformation;
    }

    /**
     * Gets field by name.
     *
     * @param string $field_key Field key.
     *
     * @return array
     */
    public function get($field_key)
    {
        if ( isset($this->fields[$field_key]) ) {
            return $this->fields[$field_key];
        }

        return null;
    }

}
