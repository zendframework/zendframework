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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Authentication;

/**
 * @category   Zend
 * @package    Zend_Authentication
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AuthenticationService
{
    /**
     * Persistent storage handler
     *
     * @var Storage\StorageInterface
     */
    protected $storage = null;

    /**
     * Authentication adapter
     *
     * @var Adapter\AdapterInterface
     */
    protected $adapter = null;

    /**
     * Constructor
     * 
     * @param  Storage\StorageInterface $storage 
     * @param  Adapter\AdapterInterface $adapter 
     */
    public function __construct(Storage\StorageInterface $storage = null, Adapter\AdapterInterface $adapter = null)
    {
        if (null !== $storage) {
            $this->setStorage($storage);
        }
        if (null !== $adapter) {
            $this->setAdapter($adapter);
        }
    }

    /**
     * Returns the authentication adapter
     *
     * The adapter does not have a default if the storage adapter has not been set.
     *
     * @return Adapter\AdapterInterface|null
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Sets the authentication adapter
     *
     * @param  Adapter\AdapterInterface $adapter
     * @return AuthenticationService Provides a fluent interface
     */
    public function setAdapter(Adapter\AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Returns the persistent storage handler
     *
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return Storage\StorageInterface
     */
    public function getStorage()
    {
        if (null === $this->storage) {
            $this->setStorage(new Storage\Session());
        }

        return $this->storage;
    }

    /**
     * Sets the persistent storage handler
     *
     * @param  Storage\StorageInterface $storage
     * @return AuthenticationService Provides a fluent interface
     */
    public function setStorage(Storage\StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Authenticates against the supplied adapter
     *
     * @param  Adapter\AdapterInterface $adapter
     * @return Result
     * @throws Exception\RuntimeException
     */
    public function authenticate(Adapter\AdapterInterface $adapter = null)
    {
        if (!$adapter) {
            if (!$adapter = $this->getAdapter()) {
                throw new Exception\RuntimeException('An adapter must be set or passed prior to calling authenticate()');
            }
        }
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
