<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\AgileZen\Resources;

use Zend\Service\AgileZen\AbstractEntity;
use Zend\Service\AgileZen\AgileZen;
use Zend\Service\AgileZen\Container;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen_Resources
 */
class Role extends AbstractEntity
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
