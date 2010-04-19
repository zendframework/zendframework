<?php

namespace Zend\Session;

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
                throw new Exception('Configuration class provided is invalid; not found');
            }
            $config = new $config;
        }
        
        $this->_setConfig($config);
        $this->_setStorage($storage);
    }

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
                throw new Exception('Class provided for configuration is invalid; not found');
            }

            $options = $config;
            $config  = new $class();
            $config->setOptions($options);
            unset($options);
        } 
        
        if (!$config instanceof Configuration) {
            throw new Exception('Configuration type provided is invalid; must implement Zend\\Session\\Configuration');
        }
        $this->_config = $config;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    protected function _setStorage($storage)
    {
        if (null === $storage) {
            $storage = new $this->_storageDefaultClass();
        }

        if (is_string($storage)) {
            if (!class_exists($storage)) {
                throw new Exception('Class provided for Storage does not exist');
            }
            $storage = new $storage();
        }

        if (!$storage instanceof Storage) {
            throw new Exception('Storage type provided is invalid; must implement Zend\\Session\\Storage');
        }

        $this->_storage = $storage;
    }

    public function getStorage()
    {
        return $this->_storage;
    }

}
