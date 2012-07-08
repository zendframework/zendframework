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

use Zend\Service\AgileZen\AgileZen;
use Zend\Service\AgileZen\AbstractEntity;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen_Resources
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Project extends AbstractEntity
{
    /**
     * Name
     * 
     * @var string
     */
    protected $name;

    /**
     * Service
     * 
     * @var AgileZen 
     */
    protected $service;

    /**
     * Description
     * 
     * @var string 
     */
    protected $description;

    /**
     * Owner
     * 
     * @var User
     */
    protected $owner;

    /**
     * Create time
     * 
     * @var string 
     */
    protected $createTime;

    /**
     * Constructor
     * 
     * @param AgileZen $service
     * @param array $data 
     */
    public function __construct(AgileZen $service, array $data)
    {
        if (!array_key_exists('id', $data)) {
             throw new Exception\InvalidArgumentException("You must pass the id of the project");
        }
        if (!array_key_exists('name', $data)) {
             throw new Exception\InvalidArgumentException("You must pass the name of the project");
        }
        
        $this->name        = $data['name'];
        $this->description = $data['description'];
        $this->createTime  = $data['createTime'];
        $this->owner       = new User($service, $data['owner']);
        $this->service     = $service;
        
        parent::__construct($data['id']);
    }

    /**
     * Get name of the project
     * 
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the description of the project
     * 
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get create time of the project
     * 
     * @return string 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Get the owner of the project
     * 
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Get the members of the project
     * 
     * @return Zend\Service\AgileZen\Container 
     */
    public function getMembers()
    {
        return $this->service->getMembers($this->id);
    }

    /**
     * Add a member to the project
     * 
     * @param string|integer $member
     * @return boolean 
     */
    public function addMember($member)
    {
        return $this->service->addProjectMember($this->id, $member);
    }

    /**
     * Remove a member from the project
     * 
     * @param  string|integer $member
     * @return boolean 
     */
    public function removeMember($member)
    {
        return $this->service->removeProjectMember($this->id, $member);
    }

    /**
     * Get the phases of the project
     * 
     * @param  array $params
     * @return \Zend\Service\AgileZen\Container 
     */
    public function getPhases($params=array())
    {
        return $this->service->getPhases($this->id, $params);
    }

    /**
     * Get the stories of the project
     * 
     * @param  array $params
     * @return \Zend\Service\AgileZen\Container 
     */
    public function getStories($params=array())
    {
        return $this->service->getStories($this->id, $params);
    }

    /**
     * Get a specific phase of the project
     * 
     * @param  string $id
     * @param  array  $params
     * @return Phase
     */
    public function getPhase($id, $params=array()) 
    {
        if (!empty($id)) {
            return $this->service->getPhase($this->id, $id, $params);
        }
    }

    /**
     * Get a specific story of the project
     * 
     * @param  string $id
     * @param  array  $params
     * @return Phase
     */
    public function getStory($id, $params=array()) 
    {
        if (!empty($id)) {
            return $this->service->getStory($this->id, $id, $params);
        }
    }

    /**
     * Check if an array of keys is valid as data set of a project
     * 
     * @param  array $keys
     * @return boolean 
     */
    public static function validKeys($keys)
    {
        $validProjectKeys = array ('id', 'name', 'description', 'details', 'createTime', 'owner');
        $validUserKeys    = array('id', 'name', 'userName', 'email');
          
        $result = (boolean) array_diff(array_keys($keys), $validProjectKeys);   
        if (isset($keys['owner'])) {
            $result = $result || (boolean) array_diff(array_keys($keys['owner']), $validUserKeys);
        }
        return !$result;
    }
}
