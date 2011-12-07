<?php

namespace Zend\Cache\Pattern;
use Zend\Cache,
    Zend\Cache\Exception\RuntimeException,
    Zend\Cache\Exception\InvalidArgumentException;

class ClassCache extends CallbackCache
{

    /**
     * The storage adapter
     *
     * @var Zend\Cache\Storage\Adapter
     */
    protected $_storage;

    /**
     * The entity
     *
     * @var null|string
     */
    protected $_entity               = null;

    /**
     * Cache by default
     *
     * @var bool
     */
    protected $_cacheByDefault       = true;

    /**
     * Cache methods
     *
     * @var array
     */
    protected $_cacheMethods         = array();

    /**
     * Non-cache methods
     *
     * @var array
     */
    protected $_nonCacheMethods      = array();

    /**
     * Constructor
     *
     * @param array|Traversable $options
     * @throws InvalidArgumentException
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        if (!$this->getEntity()) {
            throw new InvalidArgumentException("Missing option 'entity'");
        } elseif (!$this->getStorage()) {
            throw new InvalidArgumentException("Missing option 'storage'");
        }
    }

    /**
     * Get all pattern options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        $options['storage']           = $this->getStorage();
        $options['entity']            = $this->getEntity();
        $options['cache_by_default']  = $this->getCacheByDefault();
        $options['cache_methods']     = $this->getCacheMethods();
        $options['non_cache_methods'] = $this->getNonCacheMethods();
        return $options;
    }

    /**
     * Get cache storage
     *
     * return Zend\Cache\Storage\Adapter
     */
    public function getStorage()
    {
        return $this->_storage;
    }

    /**
     * Set cache storage
     *
     * @param Zend\Cache\Storage\Adapter|array|string $storage
     * @return Zend\Cache\Pattern\PatternInterface
     */
    public function setStorage($storage)
    {
        if (is_array($storage)) {
            $storage = Cache\StorageFactory::factory($storage);
        } elseif (is_string($storage)) {
            $storage = Cache\StorageFactory::adapterFactory($storage);
        } elseif ( !($storage instanceof Cache\Storage\Adapter) ) {
            throw new InvalidArgumentException(
                'The storage must be an instanceof Zend\Cache\Storage\Adapter '
              . 'or an array passed to Zend\Cache\Storage::factory '
              . 'or simply the name of the storage adapter'
            );
        }

        $this->_storage = $storage;
        return $this;
    }

    /**
     * Set the entity to cache
     *
     * @param string $entity The entity as classname
     * @return Zend\Cache\Pattern\ClassCache
     */
    public function setEntity($entity)
    {
        if (!is_string($entity)) {
            throw new InvalidArgumentException('Invalid entity, must be a classname');
        }
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Get the entity to cache
     *
     * @return null|string The classname or NULL if no entity was set
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Enable or disable caching of methods by default.
     *
     * @param boolean $flag
     * @return Zend\Cache\Pattern\ClassCache
     */
    public function setCacheByDefault($flag)
    {
        $this->_cacheByDefault = (bool)$flag;
        return $this;
    }

    /**
     * Caching methods by default enabled.
     *
     * return boolean
     */
    public function getCacheByDefault()
    {
        return $this->_cacheByDefault;
    }

    /**
     * Enable cache methods
     *
     * @param string[] $methods
     * @return Zend\Cache\Pattern\ClassCache
     */
    public function setCacheMethods(array $methods)
    {
        $this->_cacheMethods = array_values(array_unique(array_map(function ($method) {
            return strtolower($method);
        }, $methods)));

        return $this;
    }

    /**
     * Get enabled cache methods
     *
     * @return string[]
     */
    public function getCacheMethods()
    {
        return $this->_cacheMethods;
    }

    /**
     * Disable cache methods
     *
     * @param string[] $methods
     * @return Zend\Cache\Pattern\ClassCache
     */
    public function setNonCacheMethods(array $methods)
    {
        $this->_nonCacheMethods = array_values(array_unique(array_map(function ($method) {
            return strtolower($method);
        }, $methods)));

        return $this;
    }

    /**
     * Get disabled cache methods
     *
     * @return string[]
     */
    public function getNonCacheMethods()
    {
        return $this->_nonCacheMethods;
    }

    /**
     * Call and cache a class method
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @param  array  $options Cache options
     * @return mixed
     * @throws Zend\Cache\Exception
     */
    public function call($method, array $args = array(), array $options = array())
    {
        $classname = $this->getEntity();
        $method    = strtolower($method);
        $callback  = $classname . '::' . $method;

        $cache = $this->getCacheByDefault();
        if ($cache) {
            $cache = !in_array($method, $this->getNonCacheMethods());
        } else {
            $cache = in_array($method, $this->getCacheMethods());
        }

        if (!$cache) {
            if ($args) {
                return call_user_func_array($callback, $args);
            } else {
                return $classname::$method();
            }
        }

        // speed up key generation
        if (!isset($options['callback_key'])) {
            $options['callback_key'] = $callback;
        }

        return parent::call($callback, $args, $options);
    }

    /**
     * Generate a key from the method name and arguments
     *
     * @param  string   $method  The method name
     * @param  array    $args    Method arguments
     * @return string
     * @throws Zend\Cache\Exception
     */
    public function generateKey($method, array $args = array(), array $options = array())
    {
        // speed up key generation
        if (!isset($options['callback_key'])) {
            $callback = $this->getEntity() . '::' . strtolower($method);
            $options['callback_key'] = $callback;
        } else {
            $callback = $this->getEntity() . '::' . $method;
        }

        return parent::generateKey($callback, $args, $options);
    }

    /**
     * Calling a method of the entity.
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @return mixed
     * @throws Zend\Cache\Exception
     */
    public function __call($method, array $args)
    {
        return $this->call($method, $args);
    }

    /**
     * Set a static property
     *
     * @param string $name
     * @param mixed  $value
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        $class = $this->getEntity();
        $class::$name = $value;
    }

    /**
     * Get a static property
     *
     * @param string $name
     * @return mixed
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        $class = $this->getEntity();
        return $class::$name;
    }

    /**
     * Is a static property exists.
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $class = $this->getEntity();
        return isset($class::$name);
    }

    /**
     * Unset a static property
     *
     * @param string $name
     */
    public function __unset($name)
    {
        $class = $this->getEntity();
        unset($class::$name);
    }

}
