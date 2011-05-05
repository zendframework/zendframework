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
 * @package    Zend_Authentication
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Authentication\Storage;

use Zend\Authentication\Storage as AuthenticationStorage,
    Zend\Session\Container as SessionContainer,
    Zend\Session\Manager as SessionManager;

/**
 * @uses       Zend\Authentication\Storage
 * @uses       Zend\Session\Manager
 * @category   Zend
 * @package    Zend_Authentication
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Session implements AuthenticationStorage
{
    /**
     * Default session namespace
     */
    const NAMESPACE_DEFAULT = 'Zend_Auth';

    /**
     * Default session object member name
     */
    const MEMBER_DEFAULT = 'storage';

    /**
     * Object to proxy $_SESSION storage
     *
     * @var Zend\Session\Container
     */
    protected $_session;

    /**
     * Session namespace
     *
     * @var mixed
     */
    protected $_namespace;

    /**
     * Session object member
     *
     * @var mixed
     */
    protected $_member;

    /**
     * Sets session storage options and initializes session namespace object
     *
     * @param  mixed $namespace
     * @param  mixed $member
     * @return void
     */
    public function __construct(
        $namespace = self::NAMESPACE_DEFAULT, $member = self::MEMBER_DEFAULT, SessionManager $manager = null
    ) {
        $this->_namespace = $namespace;
        $this->_member    = $member;
        $this->_session   = new SessionContainer($this->_namespace, $manager);
    }

    /**
     * Returns the session namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Returns the name of the session object member
     *
     * @return string
     */
    public function getMember()
    {
        return $this->_member;
    }

    /**
     * Defined by Zend\Auth\Storage
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !isset($this->_session->{$this->_member});
    }

    /**
     * Defined by Zend\Auth\Storage
     *
     * @return mixed
     */
    public function read()
    {
        return $this->_session->{$this->_member};
    }

    /**
     * Defined by Zend\Auth\Storage
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents)
    {
        $this->_session->{$this->_member} = $contents;
    }

    /**
     * Defined by Zend\Auth\Storage
     *
     * @return void
     */
    public function clear()
    {
        unset($this->_session->{$this->_member});
    }
}
