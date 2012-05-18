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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @todo       Implement the find() method
 */
class WinCache extends AbstractAdapter
{
    /**
     * The used namespace separator
     *
     * @var string
     */
    protected $namespaceSeparator = ':';

    /**
     * Constructor
     *
     * @param  array|Traversable|WinCacheOptions $options
     * @throws Exception\ExceptionInterface
     * @return void
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('wincache')) {
            throw new Exception\ExtensionNotLoadedException("WinCache extension is not loaded");
        }

        $enabled = ini_get('wincache.ucenabled');
        if (PHP_SAPI == 'cli') {
            $enabled = $enabled && (bool) ini_get('wincache.enablecli');
        }

        if (!$enabled) {
            throw new Exception\ExtensionNotLoadedException(
                "WinCache is disabled - see 'wincache.ucenabled' and 'wincache.enablecli'"
            );
        }

        parent::__construct($options);
    }

    /* options */

    /**
     * Set options.
     *
     * @param  array|\Traversable|WinCacheOptions $options
     * @return WinCache
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof WinCacheOptions) {
            $options = new WinCacheOptions($options);
        }

        return parent::setOptions($options);
    }

    /**
     * Get options.
     *
     * @return WinCacheOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new WinCacheOptions());
        }
        return $this->options;
    }

    /* reading */

    /**
     * Internal method to get an item.
     *
     * Options:
     *  - ttl <int>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string  $normalizedKey
     * @param  array   $normalizedOptions
     * @param  boolean $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetItem(& $normalizedKey, array & $normalizedOptions, & $success = null, & $casToken = null)
    {
        $prefix      = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $internalKey = $prefix . $normalizedKey;
        $result      = wincache_ucache_get($internalKey, $success);

        if ($success) {
            $casToken = $result;
        }

        return $result;
    }

    /**
     * Internal method to get multiple items.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return array Associative array of keys and values
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $prefix       = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $fetch = wincache_ucache_get($internalKeys);

        // remove namespace prefix
        $prefixL = strlen($prefix);
        $result  = array();
        foreach ($fetch as $internalKey => & $value) {
            $result[ substr($internalKey, $prefixL) ] = $value;
        }

        return $result;
    }

    /**
     * Internal method to test if an item exists.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalHasItem(& $normalizedKey, array & $normalizedOptions)
    {
        $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        return wincache_ucache_exists($prefix . $normalizedKey);
    }

    /**
     * Get metadata of an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return array|boolean Metadata on success, false on failure
     * @throws Exception\ExceptionInterface
     *
     * @triggers getMetadata.pre(PreEvent)
     * @triggers getMetadata.post(PostEvent)
     * @triggers getMetadata.exception(ExceptionEvent)
     */
    protected function internalGetMetadata(& $normalizedKey, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;

        $info = wincache_ucache_info(true, $internalKey);
        if (isset($info['ucache_entries'][1])) {
            $metadata = $info['ucache_entries'][1];
            $this->normalizeMetadata($metadata);
            return $metadata;
        } else {
            return false;
        }
    }

    /* writing */

    /**
     * Internal method to store an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalSetItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        if (!wincache_ucache_set($internalKey, $value, $normalizedOptions['ttl'])) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new Exception\RuntimeException(
                "wincache_ucache_set('{$internalKey}', <{$type}>, {$normalizedOptions['ttl']}) failed"
            );
        }

        return true;
    }

    /**
     * Internal method to store multiple items.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedKeyValuePairs
     * @param  array $normalizedOptions
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalSetItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        $prefix  = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $prefixL = strlen($prefix);

        $internalKeyValuePairs = array();
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            $internalKey = $prefix . $normalizedKey;
            $internalKeyValuePairs[$internalKey] = $value;
        }

        $result = wincache_ucache_set($internalKeyValuePairs, null, $normalizedOptions['ttl']);

        // remove key prefic
        foreach ($result as & $key) {
            $key = substr($key, $prefixL);
        }

        return $result;
    }

    /**
     * Add an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalAddItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        if (!wincache_ucache_add($internalKey, $value, $normalizedOptions['ttl'])) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new Exception\RuntimeException(
                "wincache_ucache_add('{$internalKey}', <{$type}>, {$normalizedOptions['ttl']}) failed"
            );
        }

        return true;
    }

    /**
     * Internal method to add multiple items.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedKeyValuePairs
     * @param  array $normalizedOptions
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalAddItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        $internalKeyValuePairs = array();
        $prefix                = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            $internalKey = $prefix . $normalizedKey;
            $internalKeyValuePairs[$internalKey] = $value;
        }

        $result = wincache_ucache_add($internalKeyValuePairs, null, $normalizedOptions['ttl']);

        // remove key prefic
        foreach ($result as & $key) {
            $key = substr($key, $prefixL);
        }

        return $result;
    }

    /**
     * Internal method to replace an existing item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalReplaceItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        if (!wincache_ucache_exists($internalKey)) {
            return false;
        }

        if (!wincache_ucache_set($internalKey, $value, $normalizedOptions['ttl'])) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new Exception\RuntimeException(
                "wincache_ucache_set('{$internalKey}', <{$type}>, {$normalizedOptions['ttl']}) failed"
            );
        }

        return true;
    }

    /**
     * Internal method to remove an item.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalRemoveItem(& $normalizedKey, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        return wincache_ucache_delete($internalKey);
    }

    /**
     * Internal method to remove multiple items.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of not removed keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalRemoveItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();

        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $result = wincache_ucache_delete($internalKeys);
        if ($result === false) {
            return $normalizedKeys;
        } elseif ($result) {
            // remove key prefix
            $prefixL = strlen($prefix);
            foreach ($result as & $key) {
                $key = substr($key, $prefixL);
            }
        }

        return $result;
    }

    /**
     * Internal method to increment an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  int    $value
     * @param  array  $normalizedOptions
     * @return int|boolean The new value on success, false on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalIncrementItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        return wincache_ucache_inc($internalKey, (int)$value);
    }

    /**
     * Internal method to decrement an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  int    $value
     * @param  array  $normalizedOptions
     * @return int|boolean The new value on success, false on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalDecrementItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        return wincache_ucache_dec($internalKey, (int)$value);
    }

    /* cleaning */

    /**
     * Internal method to clear items off all namespaces.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    clearByNamespace()
     */
    protected function internalClear(& $normalizedMode, array & $normalizedOptions)
    {
        return wincache_ucache_clear();
    }

    /* status */

    /**
     * Internal method to get capabilities of this adapter
     *
     * @return Capabilities
     */
    protected function internalGetCapabilities()
    {
        if ($this->capabilities === null) {
            $marker       = new stdClass();
            $capabilities = new Capabilities(
                $this,
                $marker,
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
                        'ttl',
                        'num_hits',
                        'internal_key',
                        'mem_size'
                    ),
                    'maxTtl'             => 0,
                    'staticTtl'          => true,
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

            // update namespace separator on change option
            $this->events()->attach('option', function ($event) use ($capabilities, $marker) {
                $params = $event->getParams();

                if (isset($params['namespace_separator'])) {
                    $capabilities->setNamespaceSeparator($marker, $params['namespace_separator']);
                }
            });

            $this->capabilities     = $capabilities;
            $this->capabilityMarker = $marker;
        }

        return $this->capabilities;
    }

    /**
     * Internal method to get storage capacity.
     *
     * @param  array $normalizedOptions
     * @return array|boolean Associative array of capacity, false on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetCapacity(array & $normalizedOptions)
    {
        $mem = wincache_ucache_meminfo();
        return array(
            'free'  => $mem['memory_free'],
            'total' => $mem['memory_total'],
        );
    }

    /* internal */

    /**
     * Normalize metadata to work with WinCache
     *
     * @param  array $metadata
     * @return void
     */
    protected function normalizeMetadata(array & $metadata)
    {
        if (isset($metadata['hitcount'])) {
            $metadata['num_hits'] = $metadata['hitcount'];
            unset($metadata['hitcount']);
        }

        if (isset($metadata['ttl_seconds'])) {
            $metadata['ttl'] = $metadata['ttl_seconds'];
            unset($metadata['ttl_seconds']);
        }

         if (isset($metadata['value_size'])) {
            $metadata['mem_size'] = $metadata['value_size'];
            unset($metadata['value_size']);
        }

        if (isset($metadata['key_name'])) {
            $metadata['internal_key'] = $metadata['key_name'];
            unset($metadata['key_name']);
        }
    }
}
