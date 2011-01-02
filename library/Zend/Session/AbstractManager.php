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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractManager implements Manager
{
    /**
     * @var Configuration
     */
    protected $_config;

    /**
     * Default configuration class to use when no configuration provided
     * @var string
     */
    protected $_configDefaultClass = 'Zend\\Session\\Configuration\\SessionConfiguration';

    /**
     * @var Storage
     */
    protected $_storage;

    /**
     * Default storage class to use when no storage provided
     * @var string
     */
    protected $_storageDefaultClass = 'Zend\\Session\\Storage\\SessionStorage';


    /**
     * Constructor
     *
     * Allow passing a configuration object or class name, a storage object or 
     * class name, or an array of configuration.
     * 
     * @param  null|string|Configuration|array $config 
     * @param  null|string|Storage $storage 
     * @return void
     */
    public function __construct($config = null, $storage = null)
    {
        if ($config instanceof \Zend\Config\Config) {
            $config = $config->toArray();
        }
        if (is_array($config)) {
            foreach ($config as $key => $value) {
                switch (strtolower($key)) {
                    case 'storage':
                        if (null === $storage) {
                            $storage = $value;
                        }
                        unset($config[$key]);
                        break;
                }
            }
        } elseif (is_string($config)) {
            if (!class_exists($config)) {
                throw new Exception\InvalidArgumentException('Configuration class provided is invalid; not found');
            }
            $config = new $config;
        }
        
        $this->_setConfig($config);
        $this->_setStorage($storage);
    }

    /**
     * Set configuration object
     *
     * Allows lazy-loading a class name, passing an array of configuration to 
     * the defined default configuration class, or passing in a Configuration 
     * object. If a null value is passed, an instance of the default 
     * configuration class is created.
     * 
     * @param  null|string|array|Configuration $config 
     * @return void
     */
    protected function _setConfig($config)
    {
        if (null === $config) {
            $config = new $this->_configDefaultClass();
        }

        if (is_array($config)) {
            $class = $this->_configDefaultClass;
            if (array_key_exists('class', $config)) {
                $class = $config['class'];
                unset($config['class']);
            }

            if (!class_exists($class)) {
                throw new Exception\InvalidArgumentException('Class provided for configuration is invalid; not found');
            }

            $options = $config;
            $config  = new $class();
            $config->setOptions($options);
            unset($options);
        } 
        
        if (!$config instanceof Configuration) {
            throw new Exception\InvalidArgumentException('Configuration type provided is invalid; must implement Zend\\Session\\Configuration');
        }
        $this->_config = $config;
    }

    /**
     * Retrieve configuration object
     * 
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Set session storage object
     *
     * Allows passing a null value, string class name, or Storage object. If a 
     * null value is passed, the default storage class will be used.
     * 
     * @param  null|string|Storage $storage 
     * @return void
     */
    protected function _setStorage($storage)
    {
        if (null === $storage) {
            $storage = new $this->_storageDefaultClass();
        }

        if (is_string($storage)) {
            if (!class_exists($storage)) {
                throw new Exception\InvalidArgumentException('Class provided for Storage does not exist');
            }
            $storage = new $storage();
        }

        if (!$storage instanceof Storage) {
            throw new Exception\InvalidArgumentException('Storage type provided is invalid; must implement Zend\\Session\\Storage');
        }

        $this->_storage = $storage;
    }

    /**
     * Retrieve storage object
     * 
     * @return Storage
     */
    public function getStorage()
    {
        return $this->_storage;
    }
}
