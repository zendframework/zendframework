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
            if (!wincache_ucache_add($internalKey, $value, $options['ttl'])) {
                if (wincache_ucache_exists($internalKey)) {
                    throw new Exception\RuntimeException("Key '{$internalKey}' already exists");
                }

                $type = is_object($value) ? get_class($value) : gettype($value);
                throw new Exception\RuntimeException(
                    "wincache_ucache_add('{$internalKey}', <{$type}>, {$options['ttl']}) failed"
                );
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Add multiple items.
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
     * @triggers addItems.pre(PreEvent)
     * @triggers addItems.post(PostEvent)
     * @triggers addItems.exception(ExceptionEvent)
     */
    public function addItems(array $keyValuePairs, array $options = array())
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

            $errKeys = wincache_ucache_add($internalKeyValuePairs, null, $options['ttl']);
            if ($errKeys!==array()) {
                throw new Exception\RuntimeException(
                    "wincache_ucache_add(<array>, null, {$options['ttl']}) failed for keys: "
                    . "'" . implode("','", array_keys($errKeys)) . "'"
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

            $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            if (!wincache_ucache_exists($internalKey)) {
                throw new Exception\ItemNotFoundException(
                    "Key '{$internalKey}' doesn't exist"
                );
            }

            if (!wincache_ucache_set($internalKey, $value, $options['ttl'])) {
                $type = is_object($value) ? get_class($value) : gettype($value);
                throw new Exception\RuntimeException(
                    "wincache_ucache_set('{$internalKey}', <{$type}>, {$options['ttl']}) failed"
                );
            }

            $result = true;
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
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            if (!wincache_ucache_delete($internalKey)) {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
                }
            }

            $result = true;
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
            $newValue    = wincache_ucache_inc($internalKey, $value);
            if ($newValue === false) {
                if (wincache_ucache_exists($internalKey)) {
                    throw new Exception\RuntimeException("wincache_ucache_inc('{$internalKey}', {$value}) failed");
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
            $newValue    = wincache_ucache_dec($internalKey, $value);
            if ($newValue === false) {
                if (wincache_ucache_exists($internalKey)) {
                    throw new Exception\RuntimeException("wincache_ucache_dec('{$internalKey}', {$value}) failed");
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

    /* find */


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

            $result = wincache_ucache_clear();
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

            $mem    = wincache_ucache_meminfo ();
            $result = array(
                'free'  => $mem['memory_free'],
                'total' => $mem['memory_total'],
            );
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
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
