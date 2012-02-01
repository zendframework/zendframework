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
 * @package    Zend\Service\AgileZen
 * @subpackage Resources
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace Zend\Service\AgileZen\Resources;

use Zend\Service\AgileZen\AgileZen,
    Zend\Service\AgileZen\Entity;

/**
 * @category   Zend
 * @package    Zend\Service\AgileZen
 * @subpackage Resources
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
     * @var Zend\Service\AgileZen\Resources\User 
     */
    protected $creator;
    /**
     * Owner
     * 
     * @var Zend\Service\AgileZen\Resources\User  
     */
    protected $owner;
    /**
     * AgileZen service
     * 
     * @var Zend\Service\AgileZen\AgileZen 
     */
    protected $service;
    /**
     * Constructor
     * 
     * @param AgileZen $service
     * @param array $data 
     */
    public function __construct(AgileZen $service,$data)
    {
        if (!($service instanceof AgileZen) || !is_array($data)) {
             throw new Exception\InvalidArgumentException("You must pass a AgileZen object and an array");
        }
        if (!array_key_exists('id', $data)) {
             throw new Exception\InvalidArgumentException("You must pass the id of the user");
        }
        
        $this->text = $data['text'];
        $this->size = $data['size'];
        $this->color = $data['color'];
        if (isset($data['priority'])) {
            $this->priority = $data['priority'];
        }
        if (isset($data['deadline'])) {
            $this->deadline = $data['deadline'];
        }    
        $this->status = $data['status'];
        $this->projectId = $data['project']['id'];
        $this->phaseId = $data['phase']['id'];
        if (isset($data['creator']) && !empty($data['creator'])) {
            $this->creator = new User($service, $data['creator']);
        }    
        if (isset($data['owner']) && !empty($data['owner'])) {
            $this->owner = new User($service, $data['owner']);
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
     * @return Zend\Service\AgileZen\Resources\Project 
     */
    public function getProject()
    {
        return $this->service->getProject($this->projectId);
    }
    /**
     * Get the phase
     * 
     * @return Zend\Service\AgileZen\Resources\Phase
     */
    public function getPhase()
    {
        return $this->service->getPhase($this->projectId, $this->phaseId);
    }
    /**
     * Get the creator
     * 
     * @return Zend\Service\AgileZen\Resources\User
     */
    public function getCreator()
    {
        return $this->creator;
    }
    /**
     * Get the owner
     * 
     * @return Zend\Service\AgileZen\Resources\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }
    /**
     * Get the tasks
     * 
     * @return Zend\Service\AgileZen\Container 
     */
    public function getTasks()
    {
        return $this->service->getTasks($this->projectId, $this->id);
    }
    /**
     * Get a task
     * 
     * @param  integer $taskId
     * @return Zend\Service\AgileZen\Resources\Task 
     */
    public function getTask($taskId) 
    {
        return $this->service->getTask($this->projectId, $this->id, $taskId);
    }
    /**
     * Add a task
     * 
     * @param  array $data
     * @return Zend\Service\AgileZen\Resources\Task  
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
     * @return Zend\Service\AgileZen\Resources\Task 
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
     * @return Zend\Service\AgileZen\Container  
     */
    public function getComments()
    {
        return $this->service->getComments($this->projectId, $this->id);
    }
    /**
     * Get a comment
     * 
     * @param  integer $commentId
     * @return Zend\Service\AgileZen\Resources\Comment
     */
    public function getComment($commentId)
    {
        return $this->service->getComment($this->projectId, $this->id, $commentId);
    }
    /**
     * Add a comment
     * 
     * @param  array $data
     * @return Zend\Service\AgileZen\Resources\Comment 
     */
    public function addComment($data)
    {
        return $this->service->addComment($this->projectId, $this->id, $data);
    }
    /**
     * Update a comment
     * 
     * @param  array $data
     * @return Zend\Service\AgileZen\Resources\Comment 
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
}