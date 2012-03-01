<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-webat this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Session;

/**
 * Base Manager implementation
 *
 * Defines common constructor logic and getters for Storage and Configuration
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractManager implements Manager
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * Default configuration class to use when no configuration provided
     * @var string
     */
    protected $configDefaultClass = 'Zend\\Session\\Configuration\\SessionConfiguration';

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * Default storage class to use when no storage provided
     * @var string
     */
    protected $storageDefaultClass = 'Zend\\Session\\Storage\\SessionStorage';

    /**
     * @var SaveHandler
     */
    protected $saveHandler;


    /**
     * Constructor
     *
     * Allow passing a configuration object or class name, a storage object or 
     * class name, or an array of configuration.
     * 
     * @param  Configuration $config 
     * @param  Storage $storage 
     * @param  SaveHandler $saveHandler
     * @return void
     */
    public function __construct(Configuration $config = null, Storage $storage = null, SaveHandler $saveHandler = null)
    {
        $this->setConfig($config);
        $this->setStorage($storage);
        if ($saveHandler) {
            $this->setSaveHandler($saveHandler);
        }
    }

    /**
     * Set configuration object
     *
     * @param  null|Configuration $config 
     * @return void
     */
    public function setConfig(Configuration $config = null)
    {
        if (null === $config) {
            $config = new $this->configDefaultClass();
            if (!$config instanceof Configuration) {
                throw new Exception\InvalidArgumentException('Default configuration type provided is invalid; must implement Zend\\Session\\Configuration');
            }
        }

        $this->config = $config;
    }

    /**
     * Retrieve configuration object
     * 
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set session storage object
     *
     * @param  null|Storage $storage 
     * @return void
     */
    public function setStorage(Storage $storage = null)
    {
        if (null === $storage) {
            $storage = new $this->storageDefaultClass();
            if (!$storage instanceof Storage) {
                throw new Exception\InvalidArgumentException('Default storage type provided is invalid; must implement Zend\\Session\\Storage');
            }
        }

        $this->storage = $storage;
    }

    /**
     * Retrieve storage object
     * 
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set session save handler object
     *
     * @param SaveHandler $saveHandler
     * @return void
     */
    public function setSaveHandler(SaveHandler $saveHandler)
    {
        if ($saveHandler === null) {
            return ;
        }
        $this->saveHandler = $saveHandler;
    }

    /**
     * Get SaveHandler Object
     *
     * @return SaveHandler
     */
    public function getSaveHandler()
    {
        return $this->saveHandler;
    }
}
