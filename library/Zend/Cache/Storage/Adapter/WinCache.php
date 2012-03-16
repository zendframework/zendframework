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
     * @throws Exception
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
     * @param  array|Traversable|WinCacheOptions $options
     * @return WinCache
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof WinCacheOptions) {
            $options = new WinCacheOptions($options);
        }

        $this->options = $options;
        return $this;
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
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item or return false
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return mixed Data on success or false on failure
     * @throws Exception
     */
    protected function internalGetItem(& $normalizedKey, array & $normalizedOptions)
    {
        $prefix      = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $internalKey = $prefix . $normalizedKey;
        $success     = false;
        $result      = wincache_ucache_get($internalKey, $success);
        if (!$success) {
            if (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
            }
        } else {
            if (array_key_exists('token', $normalizedOptions)) {
                $normalizedOptions['token'] = $result;
            }
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
     * @return array Associative array of existing keys and values
     * @throws Exception
     */
    protected function internalGetItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $prefix       = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $fetch = wincache_ucache_get($internalKeys);
        if (!$normalizedOptions['ignore_missing_items']) {
            if (count($normalizedKeys) != count($fetch)) {
                $missing = implode("', '", array_diff($internalKeys, array_keys($fetch)));
                throw new Exception\ItemNotFoundException('Keys not found: ' . $missing);
            }
        }

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
     * @throws Exception
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
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return array|boolean Metadata or false on failure
     * @throws Exception
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
        }

        if (!$metadata) {
            if (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
            }
            return false;
        }

        $this->normalizeMetadata($metadata);
        return $metadata;
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
     * @throws Exception
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
     * @return boolean
     * @throws Exception
     */
    protected function internalSetItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();

        $internalKeyValuePairs = array();
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            $internalKey = $prefix . $normalizedKey;
            $internalKeyValuePairs[$internalKey] = $value;
        }

        $errKeys = wincache_ucache_set($internalKeyValuePairs, null, $normalizedOptions['ttl']);
        if ($errKeys) {
            throw new Exception\RuntimeException(
                "wincache_ucache_set(<array>, null, {$normalizedOptions['ttl']}) failed for keys: "
                . "'" . implode("','", array_keys($errKeys)) . "'"
            );
        }

        return true;
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
     * @throws Exception
     */
    protected function internalAddItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        if (!wincache_ucache_add($internalKey, $value, $normalizedOptions['ttl'])) {
            // TODO: check if this is really needed
            if (wincache_ucache_exists($internalKey)) {
                throw new Exception\RuntimeException("Key '{$internalKey}' already exists");
            }

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
     * @return boolean
     * @throws Exception
     */
    protected function internalAddItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        $internalKeyValuePairs = array();
        $prefix                = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            $internalKey = $prefix . $normalizedKey;
            $internalKeyValuePairs[$internalKey] = $value;
        }

        $errKeys = wincache_ucache_add($internalKeyValuePairs, null, $normalizedOptions['ttl']);
        if ($errKeys !== array()) {
            throw new Exception\RuntimeException(
                "wincache_ucache_add(<array>, null, {$normalizedOptions['ttl']}) failed for keys: "
                . "'" . implode("','", array_keys($errKeys)) . "'"
            );
        }

        return true;
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
     * @throws Exception
     */
    protected function internalReplaceItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        if (!wincache_ucache_exists($internalKey)) {
            throw new Exception\ItemNotFoundException(
                "Key '{$internalKey}' doesn't exist"
            );
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
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     */
    protected function internalRemoveItem(& $normalizedKey, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        if (!wincache_ucache_delete($internalKey) && !$normalizedOptions['ignore_missing_items']) {
            throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
        }

        return true;
    }

    /**
     * Internal method to increment an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item
     *
     * @param  string $normalizedKey
     * @param  int    $value
     * @param  array  $normalizedOptions
     * @return int|boolean The new value or false on failure
     * @throws Exception
     */
    protected function internalIncrementItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        $value       = (int)$value;
        $newValue    = wincache_ucache_inc($internalKey, $value);
        if ($newValue === false) {
            if (wincache_ucache_exists($internalKey)) {
                throw new Exception\RuntimeException("wincache_ucache_inc('{$internalKey}', {$value}) failed");
            } elseif (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException(
                    "Key '{$internalKey}' not found"
                );
            }

            $newValue = $value;
            $this->addItem($normalizedKey, $newValue, $normalizedOptions);
        }

        return $newValue;
    }

    /**
     * Internal method to decrement an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item
     *
     * @param  string $normalizedKey
     * @param  int    $value
     * @param  array  $normalizedOptions
     * @return int|boolean The new value or false on failure
     * @throws Exception
     */
    protected function internalDecrementItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        $value       = (int)$value;
        $newValue    = wincache_ucache_dec($internalKey, $value);
        if ($newValue === false) {
            if (wincache_ucache_exists($internalKey)) {
                throw new Exception\RuntimeException("wincache_ucache_inc('{$internalKey}', {$value}) failed");
            } elseif (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException(
                    "Key '{$internalKey}' not found"
                );
            }

            $newValue = -$value;
            $this->addItem($normalizedKey, $newValue, $normalizedOptions);
        }

        return $newValue;
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
     * @throws Exception
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
        }

        return $this->capabilities;
    }

    /**
     * Internal method to get storage capacity.
     *
     * @param  array $normalizedOptions
     * @return array|boolean Capacity as array or false on failure
     * @throws Exception
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
    protected function normalizeMetadata(array &$metadata)
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
