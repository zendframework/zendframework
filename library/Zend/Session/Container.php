<?php

namespace Zend\Session;

use ArrayObject;

class Container extends ArrayObject
{
    protected $_name;
    protected $_manager;

    public function __construct($name = 'Default', $manager = null)
    {
        if (!preg_match('/^[a-z][a-z0-9_]+$/i', $name)) {
            throw new Exception('Name passed to container is invalid; must consist of alphanumerics and underscores only');
        }
        $this->_name = $name;
        $this->_setManager($manager);
        parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getManager()
    {
        return $this->_manager;
    }

    protected function _setManager($manager)
    {
        if (null === $manager) {
            $manager = new Manager();
        }
        if (!$manager instanceof Manager) {
            throw new Exception('Manager provided is invalid; must extend Manager class');
        }
        $this->_manager = $manager;
        return $this;
    }

    protected function _getStorage()
    {
        return $this->getManager()->getStorage();
    }

    protected function _createContainer()
    {
        return new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
    }

    protected function _verifyNamespace()
    {
        $storage = $this->_getStorage();
        $name    = $this->getName();
        if (!isset($storage[$name])) {
            $storage[$name] = $this->_createContainer();
        }
        if (!is_array($storage[$name]) && !$storage[$name] instanceof ArrayObject) {
            throw new Exception('Container cannot write to storage due to type mismatch');
        }
        return $storage;
    }

    protected function _expireKeys($key)
    {
        $storage = $this->_verifyNamespace();
        $name    = $this->getName();

        // Return early if key not found
        if (!isset($storage[$name][$key])) {
            return true;
        }

        $metadata = $storage->getMetadata($name);
        if (is_array($metadata) 
            && isset($metadata['EXPIRE']) 
            && ($_SERVER['REQUEST_TIME'] > $metadata['EXPIRE'])
        ) {
            unset($metadata['EXPIRE']);
            $storage->setMetadata($name, $metadata, true);
            $storage[$name] = $this->_createContainer();
            return true;
        }

        if (is_array($metadata) 
            && isset($metadata['EXPIRE_KEYS']) 
            && isset($metadata['EXPIRE_KEYS'][$key]) 
            && ($_SERVER['REQUEST_TIME'] > $metadata['EXPIRE_KEYS'][$key])
        ) {
            unset($metadata['EXPIRE_KEYS'][$key]);
            $storage->setMetadata($name, $metadata, true);
            unset($storage[$name][$key]);
            return true;
        }

        return false;
    }

    public function offsetSet($key, $value)
    {
        $this->_expireKeys($key);
        $storage = $this->_verifyNamespace();
        $name    = $this->getName();
        $storage[$name][$key] = $value;
    }

    public function offsetExists($key)
    {
        $storage = $this->_verifyNamespace();
        $name    = $this->getName();

        // Return early if the key isn't set
        if (!isset($storage[$name][$key])) {
            return false;
        }

        $expired = $this->_expireKeys($key);
        return !$expired;
    }

    public function offsetGet($key)
    {
        if (!$this->offsetExists($key)) {
            return null;
        }
        $storage = $this->_getStorage();
        $name    = $this->getName();
        return $storage[$name][$key];
    }

    public function offsetUnset($key)
    {
        if (!$this->offsetExists($key)) {
            return;
        }
        $storage = $this->_getStorage();
        $name    = $this->getName();
        unset($storage[$name][$key]);
    }

    public function setExpirationSeconds($ttl, $vars = null)
    {
        $storage = $this->_getStorage();
        $ts      = $_SERVER['REQUEST_TIME'] + $ttl;
        if (null === $vars) {
            $data = array('EXPIRE' => $ts);
        } elseif (is_scalar($vars)) {
            if (!$this->offsetExists($vars)) {
                return $this;
            }
            $data = array('EXPIRE_KEYS' => array($vars => $ts));
        } elseif (is_array($vars)) {
            // Cannot pass "$this" to a lambda
            $container = $this;

            // Filter out any items not in our container
            $expires   = array_filter($vars, function ($value) use ($container) {
                return $container->offsetExists($value);
            });

            // Map item keys => timestamp
            $expires   = array_flip($expires);
            $expires   = array_map(function ($value) use ($ts) {
                return $ts;
            }, $expires);

            // Create metadata array to merge in
            $data = array('EXPIRE_KEYS' => $expires);
        } else {
            throw new Exception('Unknown data provided as second argument to ' . __METHOD__);
        }

        $storage->setMetadata(
            $this->getName(), 
            $data
        );
        return $this;
    }
}
