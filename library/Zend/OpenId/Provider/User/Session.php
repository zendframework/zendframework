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
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Provider
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\OpenId\Provider\User;

/**
 * Class to get/store information about logged in user in Web Browser using
 * PHP session
 *
 * @uses       Zend\OpenId\Provider\User\AbstractUser
 * @uses       Zend\Session\Container
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Provider
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Session extends AbstractUser
{
    /**
     * Reference to an implementation of Zend_Session_Namespace object
     *
     * @var \Zend\Session\Container $_session
     */
    private $_session = null;

    /**
     * Creates \Zend\OpenId\Provider\User\Session object with given session
     * namespace or creates new session namespace named "openid"
     *
     * @param \Zend\Session\Container $session
     */
    public function __construct(\Zend\Session\Container $session = null)
    {
        if ($session === null) {
            $this->_session = new \Zend\Session\Container("openid");
        } else {
            $this->_session = $session;
        }
    }

    /**
     * Stores information about logged in user in session data
     *
     * @param string $id user identity URL
     * @return bool
     */
    public function setLoggedInUser($id)
    {
        $this->_session->logged_in = $id;
        return true;
    }

    /**
     * Returns identity URL of logged in user or false
     *
     * @return mixed
     */
    public function getLoggedInUser()
    {
        if (isset($this->_session->logged_in)) {
            return $this->_session->logged_in;
        }
        return false;
    }

    /**
     * Performs logout. Clears information about logged in user.
     *
     * @return bool
     */
    public function delLoggedInUser()
    {
        unset($this->_session->logged_in);
        return true;
    }

}
