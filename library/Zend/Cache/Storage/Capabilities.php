<?php

namespace Zend\Cache\Storage;

use Zend\Cache\Exception\MissingKeyException;

use Zend\EventManager\EventManager,
    Zend\Cache\Exception\InvalidArgumentException,
    Zend\Cache\Exception\MissingDependencyException;

class Capabilities
{

    /**
     * A marker to set/change capabilities
     *
     * @var stdClass
     */
    protected $_marker;

    /**
     * The event manager
     *
     * @var null|Zend\EventManager\EventManager
     */
    protected $_eventManager;

   /**
    * Capability property
    *
    * If it's NULL the capability isn't set and the getter
    * returns the base capability or the default value.
    *
    * @var null|mixed
    */
    protected $_supportedDatatypes;

    /**
     * Supported metdata
     */
    protected $_supportedMetadata;

    /**
     * Max ttl
     */
    protected $_maxTtl;

    /**
     * Static ttl
     */
    protected $_staticTtl;

    /**
     * Ttl precision
     */
    protected $_ttlPrecision;

    /**
     * Use request time
     */
    protected $_useRequestTime;

    /**
     * Expire read
     */
    protected $_expiredRead;

    /**
     * Max key length
     */
    protected $_maxKeyLength;

    /**
     * Namespace is prefix
     */
    protected $_namespaceIsPrefix;

    /**
     * Namespace separator
     */
    protected $_namespaceSeparator;

    /**
     * Iterable
     */
    protected $_iterable;

    /**
     * Clear all namespaces
     */
    protected $_clearAllNamespaces;

    /**
     * Clear by namespace
     */
    protected $_clearByNamespace;

    /**
     * Base capabilities
     *
     * @var null|Zend\Cache\Storage\Capabilities
     */
    protected $_baseCapabilities;

    /**
     * Constructor
     *
     * @param \stdClass $marker
     * @param array $capabilities
     * @param null|Zend\Cache\Storage\Capabilities $baseCapabilities
     */
    public function __construct(
        \stdClass $marker,
        array $capabilities = array(),
        Capabilities $baseCapabilities = null
    ) {
        $this->_marker = $marker;
        $this->_baseCapabilities = $baseCapabilities;
        foreach ($capabilities as $name => $value) {
            $this->_setCapability($marker, $name, $value);
        }
    }

    /**
     * Returns if the dependency of Zend\EventManager is available
     *
     * @return boolean
     */
    public function hasEventManager()
    {
        return ($this->_eventManager !== null || class_exists('Zend\EventManager\EventManager'));
    }

    /**
     * Get the event manager
     *
     * @return Zend\EventManager\EventManager
     * @throws Zend\Cache\Exception\MissingDependencyException
     */
    public function getEventManager()
    {
        if ($this->_eventManager === null) {
            if (!class_exists('Zend\EventManager\EventManager')) {
                throw new MissingDependencyException('Zend\EventManager not found');
            }

            // create a new event manager object
            $eventManager = new EventManager();

            // trigger change event on change of a base capability
            if ($this->_baseCapabilities && $this->_baseCapabilities->hasEventManager()) {
                $onChange = function ($event) use ($eventManager)  {
                    $eventManager->trigger('change', $event->getTarget(), $event->getParams());
                };
                $this->_baseCapabilities->getEventManager()->attach('change', $onChange);
            }

            // register event manager
            $this->_eventManager = $eventManager;
        }
        return $this->_eventManager;
    }

    /**
     * Get supported datatypes
     *
     * @return array
     */
    public function getSupportedDatatypes()
    {
        return $this->_getCapability('supportedDatatypes', array(
            'NULL'     => false,
            'boolean'  => false,
            'integer'  => false,
            'double'   => false,
            'string'   => true,
            'array'    => false,
            'object'   => false,
            'resource' => false
        ));
    }

    /**
     * Set supported datatypes
     *
     * @param \stdClass $marker
     * @param array $datatypes
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setSupportedDatatypes(\stdClass $marker, array $datatypes)
    {
        $allTypes = array(
            'NULL', 'boolean', 'integer', 'double',
            'string', 'array', 'object', 'resource'
        );

        // check/normalize datatype values
        foreach ($datatypes as $type => &$toType) {
            if (!in_array($type, $allTypes)) {
                throw new InvalidArgumentException("Unknown datatype '{$type}'");
            }

            if (is_string($toType)) {
                $toType = strtolower($toType);
                if (!in_array($toType, $allTypes)) {
                    throw new InvalidArgumentException("Unknown datatype '{$toType}'");
                }
            } else {
                $toType = (bool)$toType;
            }
        }

        // add missing datatypes as not supported
        $missingTypes = array_diff($allTypes, array_keys($datatypes));
        foreach ($missingTypes as $type) {
            $datatypes[type] = false;
        }

        return $this->_setCapability($marker, 'supportedDatatypes', $datatypes);
    }

    /**
     * Get supported metadata
     *
     * @return array
     */
    public function getSupportedMetadata()
    {
        return $this->_getCapability('supportedMetadata', array());
    }

    /**
     * Set supported metadata
     *
     * @param \stdClass $marker
     * @param string[] $metadata
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setSupportedMetadata(\stdClass $marker, array $metadata)
    {
        foreach ($metadata as $name) {
            if (!is_string($name)) {
                throw new InvalidArgumentException('$metadata must be an array of strings');
            }
        }
        return $this->_setCapability($marker, 'supportedMetadata', $metadata);
    }

    /**
     * Get maximum supported time-to-live
     *
     * @return int 0 means infinite
     */
    public function getMaxTtl()
    {
        return $this->_getCapability('maxTtl', 0);
    }

    /**
     * Set maximum supported time-to-live
     *
     * @param \stdClass $marker
     * @param int $maxTtl
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setMaxTtl(\stdClass $marker, $maxTtl)
    {
        $maxTtl = (int)$maxTtl;
        if ($maxTtl < 0) {
            throw new InvalidArgumentException('$maxTtl must be greater or equal 0');
        }
        return $this->_setCapability($marker, 'maxTtl', $maxTtl);
    }

    /**
     * Is the time-to-live handled static (on write)
     * or dynamic (on read)
     *
     * @return boolean
     */
    public function getStaticTtl()
    {
        return $this->_getCapability('staticTtl', false);
    }

    /**
     * Set if the time-to-live handled static (on write)
     * or dynamic (on read)
     *
     * @param \stdClass $marker
     * @param boolean $flag
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setStaticTtl(\stdClass $marker, $flag)
    {
        return $this->_setCapability($marker, 'staticTtl', (bool)$flag);
    }

    /**
     * Get time-to-live precision
     *
     * @return float
     */
    public function getTtlPrecision()
    {
        return $this->_getCapability('ttlPrecision', 1);
    }

    /**
     * Set time-to-live precision
     *
     * @param \stdClass $marker
     * @param float $ttlPrecision
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setTtlPrecision(\stdClass $marker, $ttlPrecision)
    {
        $ttlPrecision = (float)$ttlPrecision;
        if ($ttlPrecision <= 0) {
            throw new InvalidArgumentException('$ttlPrecision must be greater than 0');
        }
        return $this->_setCapability($marker, 'ttlPrecision', $ttlPrecision);
    }

    /**
     * Get use request time
     *
     * @return boolean
     */
    public function getUseRequestTime()
    {
        return $this->_getCapability('useRequestTime', false);
    }

    /**
     * Set use request time
     *
     * @param \stdClass $marker
     * @param boolean $flag
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setUseRequestTime(\stdClass $marker, $flag)
    {
        return $this->_setCapability($marker, 'useRequestTime', (bool)$flag);
    }

    /**
     * Get if expired items are readable
     *
     * @return boolean
     */
    public function getExpiredRead()
    {
        return $this->_getCapability('expiredRead', false);
    }

    /**
     * Set if expired items are readable
     *
     * @param \stdClass $marker
     * @param boolean $flag
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setExpiredRead(\stdClass $marker, $flag)
    {
        return $this->_setCapability($marker, 'expiredRead', (bool)$flag);
    }

    /**
     * Get maximum key lenth
     *
     * @return int -1 means unknown, 0 means infinite
     */
    public function getMaxKeyLength()
    {
        return $this->_getCapability('maxKeyLength', -1);
    }

    /**
     * Set maximum key lenth
     *
     * @param \stdClass $marker
     * @param int $maxKeyLength
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setMaxKeyLength(\stdClass $marker, $maxKeyLength)
    {
        $maxKeyLength = (int)$maxKeyLength;
        if ($maxKeyLength < -1) {
            throw new InvalidArgumentException('$maxKeyLength must be greater or equal than -1');
        }
        return $this->_setCapability($marker, 'maxKeyLength', $maxKeyLength);
    }

    /**
     * Get if namespace support is implemented as prefix
     *
     * @return boolean
     */
    public function getNamespaceIsPrefix()
    {
        return $this->_getCapability('namespaceIsPrefix', true);
    }

    /**
     * Set if namespace support is implemented as prefix
     *
     * @param \stdClass $marker
     * @param boolean $flag
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setNamespaceIsPrefix(\stdClass $marker, $flag)
    {
        return $this->_setCapability($marker, 'namespaceIsPrefix', (bool)$flag);
    }

    /**
     * Get namespace separator if namespace is implemented as prefix
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->_getCapability('namespaceSeparator', '');
    }

    /**
     * Set the namespace separator if namespace is implemented as prefix
     *
     * @param \stdClass $marker
     * @param string $separator
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setNamespaceSeparator(\stdClass $marker, $separator)
    {
        return $this->_setCapability($marker, 'namespaceSeparator', (string)$separator);
    }

    /**
     * Get if items are iterable
     *
     * @return boolean
     */
    public function getIterable()
    {
        return $this->_getCapability('iterable', false);
    }

    /**
     * Set if items are iterable
     *
     * @param \stdClass $marker
     * @param boolean $flag
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setIterable(\stdClass $marker, $flag)
    {
        return $this->_setCapability($marker, 'iterable', (bool)$flag);
    }

    /**
     * Get support to clear items of all namespaces
     *
     * @return boolean
     */
    public function getClearAllNamespaces()
    {
        return $this->_getCapability('clearAllNamespaces', false);
    }

    /**
     * Set support to clear items of all namespaces
     *
     * @param \stdClass $marker
     * @param boolean $flag
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setClearAllNamespaces(\stdClass $marker, $flag)
    {
        return $this->_setCapability($marker, 'clearAllNamespaces', (bool)$flag);
    }

    /**
     * Get support to clear items by namespace
     *
     * @return boolean
     */
    public function getClearByNamespace()
    {
        return $this->_getCapability('clearByNamespace', false);
    }

    /**
     * Set support to clear items by namespace
     *
     * @param \stdClass $marker
     * @param boolean $flag
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     */
    public function setClearByNamespace(\stdClass $marker, $flag)
    {
        return $this->_setCapability($marker, 'clearByNamespace', (bool)$flag);
    }

    /**
     * Get a capability
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected function _getCapability($name, $default = null)
    {
        $property = '_' . $name;
        if ($this->$property !== null) {
            return $this->$property;
        } elseif ($this->_baseCapabilities) {
            $getMethod = 'get' . $name;
            return $this->_baseCapabilities->$getMethod();
        }
        return $default;
    }

    /**
     * Change a capability
     *
     * @param \stdClass $marker
     * @param string $name
     * @param mixed $value
     * @return Zend\Cache\Storage\Capabilities Fluent interface
     * @throws InvalidArgumentException
     */
    protected function _setCapability(\stdClass $marker, $name, $value)
    {
        if ($this->_marker !== $marker) {
            throw new InvalidArgumentException('Invalid marker');
        }

        $property = '_' . $name;
        if ($this->$property !== $value) {
            $this->$property = $value;
            $this->getEventManager()->trigger('change', $this, array(
                $name => $value
            ));
        }

        return $this;
    }

}
