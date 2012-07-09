<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OpenId
 */

namespace Zend\OpenId\Provider\User;

/**
 * Class to get/store information about logged in user in Web Browser using
 * PHP session
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Provider
 */
class Session extends AbstractUser
{
    /**
     * Reference to an implementation of Zend\Session\Container object
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
