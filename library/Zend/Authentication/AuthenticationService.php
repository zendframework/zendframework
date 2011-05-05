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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Authentication;

/**
 * @uses       Zend\Authentication\Storage\Session
 * @category   Zend
 * @package    Zend_Authentication
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AuthenticationService
{
    /**
     * Persistent storage handler
     *
     * @var Zend\Authentication\Storage
     */
    protected $_storage = null;

    /**
     * Constructor
     * 
     * @param  Storage $storage 
     * @return void
     */
    public function __construct(Storage $storage = null)
    {
        if (null !== $storage) {
            $this->setStorage($storage);
        }
    }

    /**
     * Returns the persistent storage handler
     *
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return Zend\Authentication\Storage
     */
    public function getStorage()
    {
        if (null === $this->_storage) {
            $this->setStorage(new Storage\Session());
        }

        return $this->_storage;
    }

    /**
     * Sets the persistent storage handler
     *
     * @param  Zend\Authentication\Storage $storage
     * @return Zend\Authentication\AuthenticationService Provides a fluent interface
     */
    public function setStorage(Storage $storage)
    {
        $this->_storage = $storage;
        return $this;
    }

    /**
     * Authenticates against the supplied adapter
     *
     * @param  Zend\Authentication\Adapter $adapter
     * @return Zend\Authentication\Result
     */
    public function authenticate(Adapter $adapter)
    {
        $result = $adapter->authenticate();

        /**
         * ZF-7546 - prevent multiple succesive calls from storing inconsistent results
         * Ensure storage has clean state
         */
        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }

        if ($result->isValid()) {
            $this->getStorage()->write($result->getIdentity());
        }

        return $result;
    }

    /**
     * Returns true if and only if an identity is available from storage
     *
     * @return boolean
     */
    public function hasIdentity()
    {
        return !$this->getStorage()->isEmpty();
    }

    /**
     * Returns the identity from storage or null if no identity is available
     *
     * @return mixed|null
     */
    public function getIdentity()
    {
        $storage = $this->getStorage();

        if ($storage->isEmpty()) {
            return null;
        }

        return $storage->read();
    }

    /**
     * Clears the identity from persistent storage
     *
     * @return void
     */
    public function clearIdentity()
    {
        $this->getStorage()->clear();
    }
}
