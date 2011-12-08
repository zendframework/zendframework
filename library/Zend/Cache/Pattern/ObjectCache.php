<?php

namespace Zend\Cache\Pattern;
use Zend\Cache,
    Zend\Cache\Exception\RuntimeException,
    Zend\Cache\Exception\InvalidArgumentException;

class ObjectCache extends CallbackCache
{

    /**
     * The entity
     *
     * @var null|object
     */
    protected $entity = null;

    /**
     * The entity key
     *
     * @var null|string
     */
    protected $entityKey = null;

    /**
     * Cache by default
     *
     * @var bool
     */
    protected $cacheByDefault = true;

    /**
     * Cache methods
     *
     * @var array
     */
    protected $cacheMethods = array();

    /**
     * Non-cache methods
     *
     * @var array
     */
    protected $nonCacheMethods = array('__tostring');

    /**
     * Cache magic properties
     *
     * @var bool
     */
    protected $cacheMagicProperties = false;

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
        $options['entity']                 = $this->getEntity();
        $options['entity_key']             = $this->getEntityKey();
        $options['cache_by_default']       = $this->getCacheByDefault();
        $options['cache_methods']          = $this->getCacheMethods();
        $options['non_cache_methods']      = $this->getNonCacheMethods();
        $options['cache_magic_properties'] = $this->getCacheMagicProperties();
        return $options;
    }

    /**
     * Set the entity to cache
     *
     * @param object $entity
     * @return Zend\Cache\Pattern\ObjectCache
     */
    public function setEntity($entity)
    {
        if (!is_object($entity)) {
            throw new InvalidArgumentException('Invalid entity, must be an object');
        }
        $this->entity = $entity;
        return $this;
    }

    /**
     * Get the entity to cache
     *
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set the entity key part.
     *
     * This will be used to generate the callback key
     * to speed up key generation.
     *
     * NOTE: This option has no effect if callback_key was given.
     *
     * @param null|string $key The key part as string or NULL to auto-generate
     * @return Zend\Cache\Pattern\ObjectCache
     * @see generateKey
     */
    public function setEntityKey($key)
    {
        if ($key !== null) {
            $this->entityKey = (string)$key;
        } else {
            $this->entityKey = null;
        }

        return $this;
    }

    /**
     * Get the entity key part.
     *
     * @return null|string
     * @see setEntityKey
     */
    public function getEntityKey()
    {
        return $this->entityKey;
    }

    /**
     * Enable or disable caching of methods by default.
     *
     * @param boolean $flag
     * @return Zend\Cache\Pattern\ObjectCache
     */
    public function setCacheByDefault($flag)
    {
        $this->cacheByDefault = (bool)$flag;
        return $this;
    }

    /**
     * Caching methods by default enabled.
     *
     * return boolean
     */
    public function getCacheByDefault()
    {
        return $this->cacheByDefault;
    }

    /**
     * Enable cache methods
     *
     * @param string[] $methods
     * @return Zend\Cache\Pattern\ObjectCache
     */
    public function setCacheMethods(array $methods)
    {
        $this->cacheMethods = array_values(array_unique(array_map(function ($method) {
            $method = strtolower($method);

            switch ($method) {
                case '__set':
                case '__get':
                case '__unset':
                case '__isset':
                    throw new InvalidArgumentException(
                        "Magic properties are handled by option 'cache_magic_properties'"
                    );
            }

            return $method;
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
        return $this->cacheMethods;
    }

    /**
     * Disable cache methods
     *
     * @param string[] $methods
     * @return Zend\Cache\Pattern\ObjectCache
     */
    public function setNonCacheMethods(array $methods)
    {
        $this->nonCacheMethods = array_values(array_unique(array_map(function ($method) {
            $method = strtolower($method);

            switch ($method) {
                case '__set':
                case '__get':
                case '__unset':
                case '__isset':
                    throw new InvalidArgumentException(
                        "Magic properties are handled by option 'cache_magic_properties'"
                    );
            }

            return $method;
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
        return $this->nonCacheMethods;
    }

    /**
     * Enable or disable caching of magic property calls
     *
     * @param boolean $flag
     * @return Zend\Cache\Pattern\ObjectCache
     */
    public function setCacheMagicProperties($flag)
    {
        $this->cacheMagicProperties = (bool)$flag;
        return $this;
    }

    /**
     * If caching of magic properties enabled
     *
     * @return boolean
     */
    public function getCacheMagicProperties()
    {
        return $this->cacheMagicProperties;
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
        $object = $this->getEntity();
        $method = strtolower($method);

        // handle magic methods
        switch ($method) {
            case '__set':
                $property = array_shift($args);
                $value    = array_shift($args);

                $object->{$property} = $value;

                if ( !$this->getCacheMagicProperties()
                  || property_exists($object, $property) ) {
                    // no caching if property isn't magic
                    // or caching magic properties is disabled
                    return;
                }

                // remove cached __get and __isset
                $removeKeys = null;
                if (method_exists($object, '__get')) {
                    $removeKeys[] = $this->generateKey('__get', array($property), $options);
                }
                if (method_exists($object, '__isset')) {
                    $removeKeys[] = $this->generateKey('__isset', array($property), $options);
                }
                if ($removeKeys) {
                    $this->getStorage()->removeMulti($removeKeys);
                }
                return;

            case '__get':
                $property = array_shift($args);

                if ( !$this->getCacheMagicProperties()
                  || property_exists($object, $property)) {
                    // no caching if property isn't magic
                    // or caching magic properties is disabled
                    return $object->{$property};
                }

                array_unshift($args, $property);

                if (!isset($options['callback_key'])) {
                    if ( (isset($options['entity_key']) && ($entityKey = $options['entity_key']) !== null)
                      || ($entityKey = $this->getEntityKey() !== null)) {
                        $options['callback_key'] = $entityKey . '::' . strtolower($method);
                        unset($options['entity_key']);
                    }
                }

                return parent::call(array($object, '__get'), $args, $options);

           case '__isset':
                $property = array_shift($args);

                if ( !$this->getCacheMagicProperties()
                  || property_exists($object, $property)) {
                    // no caching if property isn't magic
                    // or caching magic properties is disabled
                    return isset($object->{$property});
                }

                if (!isset($options['callback_key'])) {
                    if ( (isset($options['entity_key']) && ($entityKey = $options['entity_key']) !== null)
                      || ($entityKey = $this->getEntityKey() !== null)) {
                        $options['callback_key'] = $entityKey . '::' . strtolower($method);
                        unset($options['entity_key']);
                    }
                }

                return parent::call(array($object, '__isset'), array($property), $options);

            case '__unset':
                $property = array_shift($args);

                unset($object->{$property});

                if ( !$this->getCacheMagicProperties()
                  || property_exists($object, $property)) {
                    // no caching if property isn't magic
                    // or caching magic properties is disabled
                    return;
                }

                // remove previous cached __get and __isset calls
                $removeKeys = null;
                if (method_exists($object, '__get')) {
                    $removeKeys[] = $this->generateKey('__get', array($property), $options);
                }
                if (method_exists($object, '__isset')) {
                    $removeKeys[] = $this->generateKey('__isset', array($property), $options);
                }
                if ($removeKeys) {
                    $this->getStorage()->removeMulti($removeKeys);
                }
                return;
        }

        $cache = $this->getCacheByDefault();
        if ($cache) {
            $cache = !in_array($method, $this->getNonCacheMethods());
        } else {
            $cache = in_array($method, $this->getCacheMethods());
        }

        if (!$cache) {
            if ($args) {
                return call_user_func_array(array($object, $method), $args);
            } else {
                return $object->{$method}();
            }
        }

        if (!isset($options['callback_key'])) {
            if ( (isset($options['entity_key']) && ($entityKey = $options['entity_key']) !== null)
              || ($entityKey = $this->getEntityKey() !== null)) {
                $options['callback_key'] = $entityKey . '::' . strtolower($method);
                unset($options['entity_key']);
            }
        }

        return parent::call(array($object, $method), $args, $options);
    }

    /**
     * Generate a unique key from the method name and arguments
     *
     * @param  string   $method  The method name
     * @param  array    $args    Method arguments
     * @param  array    $options Options
     * @return string
     * @throws Zend\Cache\Exception
     */
    public function generateKey($method, array $args = array(), array $options = array())
    {
        if (!isset($options['callback_key'])) {
            if ( (isset($options['entity_key']) && ($entityKey = $options['entity_key']) !== null)
              || ($entityKey = $this->getEntityKey() !== null)) {
                $options['callback_key'] = $entityKey . '::' . strtolower($method);
                unset($options['entity_key']);
            }
        }

        return parent::generateKey(array($this->getEntity(), $method), $args, $options);
    }

    /**
     * Class method call handler
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
     * Writing data to properties.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it calls __set
     * and removes cached data of previous __get and __isset calls.
     *
     * @param string $name
     * @param mixed  $value
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        return $this->call('__set', array($name, $value));
    }

    /**
     * Reading data from properties.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it calls __get.
     *
     * @param string $name
     * @return mixed
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        return $this->call('__get', array($name));
    }

    /**
     * Checking existing properties.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it calls __get.
     *
     * @param string $name
     * @return bool
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __isset($name)
    {
        return $this->call('__isset', array($name));
    }

    /**
     * Unseting a property.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it removes
     * previous cached __isset and __get calls.
     *
     * @param string $name
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __unset($name)
    {
        return $this->call('__unset', array($name));
    }

    /**
     * Handle casting to string
     *
     * @return string
     * @see http://php.net/manual/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return $this->call('__toString');
    }

    /**
     * Handle invoke calls
     *
     * @return mixed
     * @see http://php.net/manual/language.oop5.magic.php#language.oop5.magic.invoke
     */
    public function __invoke() {
        return $this->call('__invoke', func_get_args());
    }

}