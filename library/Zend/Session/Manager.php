<?php

namespace Zend\Session;

class Manager
{
    protected $_config;
    protected $_storage;
    protected $_handler;

    public function __construct($config = null, $storage = null, $handler = null)
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
                    case 'handler':
                        if (null === $handler) {
                            $handler = $value;
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
        $this->_setHandler($handler);
    }

    protected function _setConfig($config)
    {
        if (null === $config) {
            $config = new Configuration\SessionConfiguration();
        }

        if (is_array($config)) {
            $class = 'Zend\\Session\\Configuration\\SessionConfiguration';
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

    protected function _setHandler($handler)
    {
        if (null === $handler) {
            $handler = new Handler\SessionHandler();
        }

        if (is_string($handler)) {
            if (!class_exists($handler)) {
                throw new Exception('Class provided as Handler does not exist');
            }
            $handler = new $handler();
        }

        if (!$handler instanceof Handler) {
            throw new Exception('Handler type provided is invalid; must implement Zend\\Session\\Handler');
        }

        $handler->setConfiguration($this->getConfig());
        $handler->setStorage($this->getStorage());

        $this->_handler = $handler;
    }

    public function getHandler()
    {
        return $this->_handler;
    }

    protected function _setStorage($storage)
    {
        if (null === $storage) {
            $storage = new Storage\SessionStorage();
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
