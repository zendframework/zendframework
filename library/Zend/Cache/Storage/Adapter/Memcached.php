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
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use ArrayObject,
    stdClass,
    Zend\Cache\Exception,
    Zend\Cache\Storage\Capabilities;

/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Storage
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @todo       Implement the find() method
 */
class Memcached extends AbstractAdapter
{
    /**
     * Memcached instance
     *
     * @var Memcached
     */
    protected $memcached;

    /**
     * The used namespace separator
     *
     * @var string
     */
    protected $namespaceSeparator = ':';

    /**
     * Constructor
     *
     * @param  array $options Option
     * @throws Exception
     * @return void
     */
    public function __construct()
    {
        if (!extension_loaded('memcached')) {
            throw new Exception\ExtensionNotLoadedException("Memcached extension is not loaded");
        }
        
        $this->memcached= new Memcached();
        
    }

    /* options */

    /**
     * Set options.
     *
     * @param  stringTraversable|WinCacheOptions $options
     * @return WinCache
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!is_array($options) 
            && !$options instanceof Traversable 
            && !$options instanceof MemcachedOptions    
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array, a Traversable object; '
                . 'received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
        $this->options = $options;
        return $this;
    }

    /**
     * Get options.
     *
     * @return MemcachedOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new MemcachedOptions());
        }
        return $this->options;
    }


    /* reading */

    /**
     * Get an item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  array $options
     * @return mixed Value on success and false on failure
     * @throws Exception
     *
     * @triggers getItem.pre(PreEvent)
     * @triggers getItem.post(PostEvent)
     * @triggers getItem.exception(ExceptionEvent)
     */
    public function getItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            $result      = $this->memcached->get($internalKey);
            if ($result===false) {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
                }
            } else {
                if (array_key_exists('token', $options)) {
                    $options['token'] = $result;
                }
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Get multiple items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return array Assoziative array of existing keys and values or false on failure
     * @throws Exception
     *
     * @triggers getItems.pre(PreEvent)
     * @triggers getItems.post(PostEvent)
     * @triggers getItems.exception(ExceptionEvent)
     */
    public function getItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'keys'    => & $keys,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $namespaceSep = $baseOptions->getNamespaceSeparator();
            $internalKeys = array();
            foreach ($keys as $key) {
                $internalKeys[] = $options['namespace'] . $namespaceSep . $key;
            }

            $fetch = $this->memcached->getMulti($internalKeys);
            
            if ($fetch===false) {
                throw new Exception\ItemNotFoundException('Memcached::getMulti(<array>) failed');
            }
            
            // remove namespace prefix
            $prefixL = strlen($options['namespace'] . $namespaceSep);
            $result  = array();
            foreach ($fetch as $internalKey => &$value) {
                $result[ substr($internalKey, $prefixL) ] = $value;
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Get metadata of an item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  array $options
     * @return array|boolean Metadata or false on failure
     * @throws Exception
     *
     * @triggers getMetadata.pre(PreEvent)
     * @triggers getMetadata.post(PostEvent)
     * @triggers getMetadata.exception(ExceptionEvent)
     */
    public function getMetadata($key, array $options = array())
    {
        throw new Exception\UnsupportedMethodCallException(__FUNCTION__ . ' is not supported by the adapter');
    }

    /* writing */

    /**
     * Store an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed $value
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers setItem.pre(PreEvent)
     * @triggers setItem.post(PostEvent)
     * @triggers setItem.exception(ExceptionEvent)
     */
    public function setItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            if (!$this->memcached->set($internalKey, $value, $options['ttl'])) {
                $type = is_object($value) ? get_class($value) : gettype($value);
                throw new Exception\RuntimeException(
                    "Memcached::set('{$internalKey}', <{$type}>, {$options['ttl']}) failed"
                );
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Store multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers setItems.pre(PreEvent)
     * @triggers setItems.post(PostEvent)
     * @triggers setItems.exception(ExceptionEvent)
     */
    public function setItems(array $keyValuePairs, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'keyValuePairs' => & $keyValuePairs,
            'options'       => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKeyValuePairs = array();
            $prefix                = $options['namespace'] . $baseOptions->getNamespaceSeparator();
            foreach ($keyValuePairs as $key => &$value) {
                $internalKey = $prefix . $key;
                $internalKeyValuePairs[$internalKey] = &$value;
            }

            $errKeys = $this->memcached->setMulti($internalKeyValuePairs, $options['ttl']);
            if ($errKeys==false) {
                throw new Exception\RuntimeException("Memcached::setMulti(<array>, {$options['ttl']}) failed");
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Add an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception
     *
     * @triggers addItem.pre(PreEvent)
     * @triggers addItem.post(PostEvent)
     * @triggers addItem.exception(ExceptionEvent)
     */
    public function addItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            if (!$this->memcached->add($internalKey, $value, $options['ttl'])) {
                if ($this->memcached->get($internalKey)!==false) {
                    throw new Exception\RuntimeException("Key '{$internalKey}' already exists");
                }

                $type = is_object($value) ? get_class($value) : gettype($value);
                throw new Exception\RuntimeException(
                    "Memcached::add('{$internalKey}', <{$type}>, {$options['ttl']}) failed"
                );
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Replace an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception
     *
     * @triggers replaceItem.pre(PreEvent)
     * @triggers replaceItem.post(PostEvent)
     * @triggers replaceItem.exception(ExceptionEvent)
     */
    public function replaceItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result= $this->memcached->replace($internalKey, $value, $options['ttl']);
            
            if ($result === false) {
                $type = is_object($value) ? get_class($value) : gettype($value);
                throw new Exception\RuntimeException(
                    "Memcached::replace('{$internalKey}', <{$type}>, {$options['ttl']}) failed"
                );
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Remove an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers removeItem.pre(PreEvent)
     * @triggers removeItem.post(PostEvent)
     * @triggers removeItem.exception(ExceptionEvent)
     */
    public function removeItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            
            $result= $this->memcached->delete($internalKey);
            
            if ($result === false) {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
                }
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Increment an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  int $value
     * @param  array $options
     * @return int|boolean The new value or false on failure
     * @throws Exception
     *
     * @triggers incrementItem.pre(PreEvent)
     * @triggers incrementItem.post(PostEvent)
     * @triggers incrementItem.exception(ExceptionEvent)
     */
    public function incrementItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            $value       = (int)$value;
            $newValue    = $this->memcached->increment($internalKey, $value);
            if ($newValue === false) {
                if ($this->memcached->get($internalKey)!==false) {
                    throw new Exception\RuntimeException("Memcached::increment('{$internalKey}', {$value}) failed");
                } elseif (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException(
                        "Key '{$internalKey}' not found"
                    );
                }
                
                $this->addItem($key, $value, $options);
                $newValue = $value;
            }

            return $this->triggerPost(__FUNCTION__, $args, $newValue);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Decrement an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  int $value
     * @param  array $options
     * @return int|boolean The new value or false or failure
     * @throws Exception
     *
     * @triggers decrementItem.pre(PreEvent)
     * @triggers decrementItem.post(PostEvent)
     * @triggers decrementItem.exception(ExceptionEvent)
     */
    public function decrementItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            $value       = (int)$value;
            $newValue    = $this->memcached->decrement($internalKey, $value);
            if ($newValue === false) {
                if ($this->memcached->get($internalKey)!==false) {
                    throw new Exception\RuntimeException("Memcached::decrement('{$internalKey}', {$value}) failed");
                } elseif (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException(
                        "Key '{$internalKey}' not found"
                    );
                }

                $this->addItem($key, -$value, $options);
                $newValue = -$value;
            }

            return $this->triggerPost(__FUNCTION__, $args, $newValue);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* non-blocking */


    /* cleaning */

    /**
     * Clear items off all namespaces.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - tags <array> optional
     *    - Tags to search for used with matching modes of
     *      Zend\Cache\Storage\Adapter::MATCH_TAGS_*
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Exception
     * @see clearByNamespace()
     *
     * @triggers clear.pre(PreEvent)
     * @triggers clear.post(PostEvent)
     * @triggers clear.exception(ExceptionEvent)
     */
    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);
        $args = new ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->memcached->flush();
            
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* status */

    /**
     * Get capabilities
     *
     * @return Capabilities
     *
     * @triggers getCapabilities.pre(PreEvent)
     * @triggers getCapabilities.post(PostEvent)
     * @triggers getCapabilities.exception(ExceptionEvent)
     */
    public function getCapabilities()
    {
        $args = new ArrayObject();

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if ($this->capabilities === null) {
                $this->capabilityMarker = new stdClass();
                $this->capabilities     = new Capabilities(
                    $this->capabilityMarker,
                    array(
                        'supportedDatatypes' => array(
                            'NULL'     => true,
                            'boolean'  => true,
                            'integer'  => true,
                            'double'   => true,
                            'string'   => true,
                            'array'    => true,
                            'object'   => 'object',
                            'resource' => false,
                        ),
                        'supportedMetadata' => array(
                        ),
                        'maxTtl'             => 0,
                        'staticTtl'          => false,
                        'ttlPrecision'       => 1,
                        'useRequestTime'     => false,
                        'expiredRead'        => false,
                        'namespaceIsPrefix'  => true,
                        'namespaceSeparator' => $this->getOptions()->getNamespaceSeparator(),
                        'iterable'           => false,
                        'clearAllNamespaces' => true,
                        'clearByNamespace'   => false,
                    )
                );
            }

            return $this->triggerPost(__FUNCTION__, $args, $this->capabilities);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Get storage capacity.
     *
     * @param  array $options
     * @return array|boolean Capacity as array or false on failure
     *
     * @triggers getCapacity.pre(PreEvent)
     * @triggers getCapacity.post(PostEvent)
     * @triggers getCapacity.exception(ExceptionEvent)
     */
    public function getCapacity(array $options = array())
    {
        $args = new ArrayObject(array(
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $mem    = array_pop($this->memcached->getStats());
            $result = array(
                'free'  => $mem['limit_maxbytes'] - $mem['bytes'],
                'total' => $mem['limit_maxbytes'],
            );
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* internal */
    
}
