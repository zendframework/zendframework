<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace Zend\Session;

use Zend\Session\Configuration\ConfigurationInterface as Configuration;
use Zend\Session\ManagerInterface as Manager;
use Zend\Session\SaveHandler\SaveHandlerInterface as SaveHandler;
use Zend\Session\Storage\StorageInterface as Storage;

/**
 * Base ManagerInterface implementation
 *
 * Defines common constructor logic and getters for Storage and Configuration
 *
 * @category   Zend
 * @package    Zend_Session
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
        $this->setOptions($config);
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
    public function setOptions(Configuration $config = null)
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
