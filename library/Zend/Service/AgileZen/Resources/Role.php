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
class Role extends Entity
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
     * Role access
     * 
     * @var string 
     */
    protected $access;

    /**
     * Members
     * 
     * @var Container
     */
    protected $members;

    /**
     * Project Id
     * 
     * @var integer 
     */
    protected $projectId;

    /**
     * Constructor
     * 
     * @param AgileZen $service
     * @param array $data 
     */
    public function __construct(AgileZen $service, array $data)
    {
        if (!array_key_exists('id', $data)) {
             throw new Exception\InvalidArgumentException("You must pass the id of the role");
        }
        if (!array_key_exists('name', $data)) {
             throw new Exception\InvalidArgumentException("You must pass the name of the role");
        }

        $this->name = $data['name'];
        if (isset($data['access'])) {
            $this->access = $data['access'];
        }    

        if (!empty($data['members'])) {
            $this->members = new Container($service, $data['members'], 'user');
        }    

        $this->service   = $service;
        $this->projectId = $data['projectId'];
        
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
     * Get the role access
     * 
     * @return string 
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Get the members
     * 
     * @return Container 
     */
    public function getMembers()
    {
        return $this->members;
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
