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

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen_Resources
 */
class Invite extends AbstractEntity
{
    /**
     * Create time
     *
     * @var string
     */
    protected $createTime;

    /**
     * Service
     *
     * @var AgileZen
     */
    protected $service;

    /**
     * Email
     *
     * @var string
     */
    protected $email;

    /**
     * Token
     *
     * @var string
     */
    protected $token;

    /**
     * Sender
     *
     * @var User
     */
    protected $sender;

    /**
     * Role
     *
     * @var Role
     */
    protected $role;

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
             throw new Exception\InvalidArgumentException("You must pass the id of the invite");
        }

        $this->createTime = $data['createTime'];

        $data['role']['projectId'] = $data['projectId'];

        $this->email      = $data['email'];
        $this->token      = $data['token'];
        $this->sender     = new User($service, $data['sender']);
        $this->role       = new Role($service, $data['role']);
        $this->projectId  = $data['projectId'];
        $this->service    = $service;

        parent::__construct($data['id']);
    }

    /**
     * Get the create time
     *
     * @return string
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Get the email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get the sender
     *
     * @return User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Get the role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
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
