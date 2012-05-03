<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen_Resources
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\AgileZen\Resources;

use Zend\Service\AgileZen\AgileZen,
    Zend\Service\AgileZen\Entity,
    Zend\Service\AgileZen\Container;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen_Resources
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Story extends Entity
{
    /**
     * Text
     * 
     * @var string 
     */
    protected $text;

    /**
     * Details
     * 
     * @var string 
     */
    protected $details;
    /**
     * Size
     * 
     * @var string 
     */
    protected $size;

    /**
     * Color
     * 
     * @var string 
     */
    protected $color;

    /**
     * Priority
     * 
     * @var string 
     */
    protected $priority;

    /**
     * Deadline
     * 
     * @var string 
     */
    protected $deadline;

    /**
     * Status
     * 
     * @var string 
     */
    protected $status;

    /**
     * Project Id
     * 
     * @var integer 
     */
    protected $projectId;

    /**
     * Phase Id
     * 
     * @var integer 
     */
    protected $phaseId;

    /**
     * Creator
     * 
     * @var User 
     */
    protected $creator;

    /**
     * Owner
     * 
     * @var User  
     */
    protected $owner;

    /**
     * Tags
     * 
     * @var Zend\Service\AgileZen\Container
     */
    protected $tags;
    /**
     * AgileZen service
     * 
     * @var AgileZen 
     */
    protected $service;

    /**
     * Constructor
     * 
     * @param AgileZen $service
     * @param array $data 
     */
    public function __construct(AgileZen $service, array $data)
    {
        if (!array_key_exists('id', $data)) {
             throw new Exception\InvalidArgumentException("You must pass the id of the user");
        }
        
        $this->text = $data['text'];
        if (isset($data['details'])) {
            $this->details = $data['details'];
        }
        $this->size = $data['size'];
        $this->color = $data['color'];

        if (isset($data['priority'])) {
            $this->priority = $data['priority'];
        }

        if (isset($data['deadline'])) {
            $this->deadline = $data['deadline'];
        }    

        $this->status    = $data['status'];
        $this->projectId = $data['project']['id'];
        $this->phaseId   = $data['phase']['id'];

        if (isset($data['creator']) && !empty($data['creator'])) {
            $this->creator = new User($service, $data['creator']);
        }    

        if (isset($data['owner']) && !empty($data['owner'])) {
            $this->owner = new User($service, $data['owner']);
        }
        if (isset($data['tags']) && is_array($data['tags']) && !empty($data['tags'])) {
            $this->tags = new Container($service, $data['tags'], 'tag', $this->projectId);
        }

        $this->service= $service;
        
        parent::__construct($data['id']);
    }

    /**
     * Get text
     * 
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get details
     * 
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }
    /**
     * Get size
     * 
     * @return string 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get color
     * 
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Get priority
     * 
     * @return string 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get deadline
     * 
     * @return string 
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Get status
     * 
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get the project
     * 
     * @return Project 
     */
    public function getProject()
    {
        return $this->service->getProject($this->projectId);
    }

    /**
     * Get the phase
     * 
     * @param  array $params
     * @return Phase
     */
    public function getPhase($params=array())
    {
        return $this->service->getPhase($this->projectId, $this->phaseId, $params);
    }

    /**
     * Get the creator
     * 
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Get the owner
     * 
     * @return User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Get the tasks
     * 
     * @param  array $params
     * @return \Zend\Service\AgileZen\Container 
     */
    public function getTasks($params=array())
    {
        return $this->service->getTasks($this->projectId, $this->id, $params);
    }

    /**
     * Get a task
     * 
     * @param  integer $taskId
     * @return Task 
     */
    public function getTask($taskId) 
    {
        return $this->service->getTask($this->projectId, $this->id, $taskId);
    }

    /**
     * Add a task
     * 
     * @param  array $data
     * @return Task  
     */
    public function addTask($data) 
    {
        return $this->service->addTask($this->projectId, $this->id, $data);
    }

    /**
     * Update a task
     * 
     * @param  integer $id
     * @param  array $data
     * @return Task 
     */
    public function updateTask($id, $data)
    {
        return $this->service->updateTask($this->projectId, $this->id, $id, $data);
    }

    /**
     * Remove a task
     * 
     * @param  integer $id
     * @return boolean 
     */
    public function removeTask($id)
    {
        return $this->service->removeTask($this->projectId, $this->id, $id);
    }

    /**
     * Get the comments
     * 
     * @return \Zend\Service\AgileZen\Container  
     */
    public function getComments()
    {
        return $this->service->getComments($this->projectId, $this->id);
    }

    /**
     * Get a comment
     * 
     * @param  integer $commentId
     * @return Comment
     */
    public function getComment($commentId)
    {
        return $this->service->getComment($this->projectId, $this->id, $commentId);
    }

    /**
     * Add a comment
     * 
     * @param  array $data
     * @return Comment 
     */
    public function addComment($data)
    {
        return $this->service->addComment($this->projectId, $this->id, $data);
    }

    /**
     * Update a comment
     * 
     * @param  array $data
     * @return Comment 
     */
    public function updateComment($data)
    {
        return $this->service->updateComment($this->projectId, $this->id, $data);
    }

    /**
     * Remove a comment
     * 
     * @param  integer $commentId
     * @return boolean 
     */
    public function removeComment($commentId)
    {
        return $this->service->removeComment($this->projectId, $this->id, $commentId);
    }

    /**
     * Get the project's Id
     * 
     * @return integer 
     */
    public function getProjectId()
    {
        return $this->projectId;
    }
    
    /**
     * Get tags
     * 
     * @return Zend\Service\AgileZen\Container
     */
    public function getTags()
    {
        return $this->tags;
    }
}
