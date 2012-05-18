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

use APCIterator,
    ArrayObject,
    stdClass,
    Traversable,
    Zend\Cache\Exception,
    Zend\Cache\Storage\Capabilities;

/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Storage
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Apc extends AbstractAdapter
{
    /**
     * Map selected properties on getDelayed & find
     * to APCIterator selector
     *
     * Init on constructor after ext/apc has been tested
     *
     * @var null|array
     */
    protected static $selectMap = null;

    /**
     * The used namespace separator
     *
     * @var string
     */
    protected $namespaceSeparator = ':';

    /**
     * Statement
     *
     * @var null|APCIterator
     */
    protected $stmtIterator = null;

    /**
     * Constructor
     *
     * @param  null|array|Traversable|ApcOptions $options
     * @throws Exception\ExceptionInterface
     * @return void
     */
    public function __construct($options = null)
    {
        if (version_compare('3.1.6', phpversion('apc')) > 0) {
            throw new Exception\ExtensionNotLoadedException("Missing ext/apc >= 3.1.6");
        }

        $enabled = ini_get('apc.enabled');
        if (PHP_SAPI == 'cli') {
            $enabled = $enabled && (bool) ini_get('apc.enable_cli');
        }

        if (!$enabled) {
            throw new Exception\ExtensionNotLoadedException(
                "ext/apc is disabled - see 'apc.enabled' and 'apc.enable_cli'"
            );
        }

        // init select map
        if (static::$selectMap === null) {
            static::$selectMap = array(
                // 'key'       => \APC_ITER_KEY,
                'value'     => \APC_ITER_VALUE,
                'mtime'     => \APC_ITER_MTIME,
                'ctime'     => \APC_ITER_CTIME,
                'atime'     => \APC_ITER_ATIME,
                'rtime'     => \APC_ITER_DTIME,
                'ttl'       => \APC_ITER_TTL,
                'num_hits'  => \APC_ITER_NUM_HITS,
                'ref_count' => \APC_ITER_REFCOUNT,
                'mem_size'  => \APC_ITER_MEM_SIZE,

                // virtual keys
                'internal_key' => \APC_ITER_KEY,
            );
        }

        parent::__construct($options);
    }

    /* options */

    /**
     * Set options.
     *
     * @param  array|Traversable|ApcOptions $options
     * @return Apc
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof ApcOptions) {
            $options = new ApcOptions($options);
        }

        return parent::setOptions($options);
    }

    /**
     * Get options.
     *
     * @return ApcOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new ApcOptions());
        }
        return $this->options;
    }


    /* reading */

    /**
     * Internal method to get an item.
     *
     * Options:
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
        $result      = apc_fetch($internalKey, $success);

        if (!$success) {
            return null;
        }

        $casToken = $result;
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
        $namespaceSep = $this->getOptions()->getNamespaceSeparator();
        $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();

        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $fetch = apc_fetch($internalKeys);

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
        return apc_exists($prefix . $normalizedKey);
    }

    /**
     * Internal method to test multiple items.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of found keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalHasItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $prefix       = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $exists  = apc_exists($internalKeys);
        $result  = array();
        $prefixL = strlen($prefix);
        foreach ($exists as $internalKey => $bool) {
            if ($bool === true) {
                $result[] = substr($internalKey, $prefixL);
            }
        }

        return $result;
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

        // @see http://pecl.php.net/bugs/bug.php?id=22564
        if (!apc_exists($internalKey)) {
            $metadata = false;
        } else {
            $format   = \APC_ITER_ALL ^ \APC_ITER_VALUE ^ \APC_ITER_TYPE;
            $regexp   = '/^' . preg_quote($internalKey, '/') . '$/';
            $it       = new APCIterator('user', $regexp, $format, 100, \APC_LIST_ACTIVE);
            $metadata = $it->current();
        }

        if (!$metadata) {
            return false;
        }

        $this->normalizeMetadata($metadata);
        return $metadata;
    }

    /**
     * Get metadata of multiple items
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return array Associative array of keys and metadata
     *
     * @triggers getMetadatas.pre(PreEvent)
     * @triggers getMetadatas.post(PostEvent)
     * @triggers getMetadatas.exception(ExceptionEvent)
     */
    protected function internalGetMetadatas(array & $normalizedKeys, array & $normalizedOptions)
    {
        $keysRegExp = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $keysRegExp[] = preg_quote($normalizedKey, '/');
        }
        $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $regexp = '/^' . preg_quote($prefix, '/') . '(' . implode('|', $keysRegExp) . ')' . '$/';
        $format = \APC_ITER_ALL ^ \APC_ITER_VALUE ^ \APC_ITER_TYPE;

        $it      = new APCIterator('user', $regexp, $format, 100, \APC_LIST_ACTIVE);
        $result  = array();
        $prefixL = strlen($prefix);
        foreach ($it as $internalKey => $metadata) {
            // @see http://pecl.php.net/bugs/bug.php?id=22564
            if (!apc_exists($internalKey)) {
                continue;
            }

            $this->normalizeMetadata($metadata);
            $result[ substr($internalKey, $prefixL) ] = & $metadata;
        }

        return $result;
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
        $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $internalKey = $prefix . $normalizedKey;
        if (!apc_store($internalKey, $value, $normalizedOptions['ttl'])) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new Exception\RuntimeException(
                "apc_store('{$internalKey}', <{$type}>, {$normalizedOptions['ttl']}) failed"
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
        $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();

        $internalKeyValuePairs = array();
        foreach ($normalizedKeyValuePairs as $normalizedKey => &$value) {
            $internalKey = $prefix . $normalizedKey;
            $internalKeyValuePairs[$internalKey] = &$value;
        }

        $failedKeys = apc_store($internalKeyValuePairs, null, $normalizedOptions['ttl']);
        $failedKeys = array_keys($failedKeys);

        // remove prefix
        $prefixL = strlen($prefix);
        foreach ($failedKeys as & $key) {
            $key = substr($key, $prefixL);
        }

        return $failedKeys;
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
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalAddItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator() . $normalizedKey;
        if (!apc_add($internalKey, $value, $normalizedOptions['ttl'])) {
            if (apc_exists($internalKey)) {
                return false;
            }

            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new Exception\RuntimeException(
                "apc_add('{$internalKey}', <{$type}>, {$normalizedOptions['ttl']}) failed"
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

        $failedKeys = apc_add($internalKeyValuePairs, null, $normalizedOptions['ttl']);
        $failedKeys = array_keys($failedKeys);

        // remove prefix
        $prefixL = strlen($prefix);
        foreach ($failedKeys as & $key) {
            $key = substr($key, $prefixL);
        }

        return $failedKeys;
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
        if (!apc_exists($internalKey)) {
            return false;
        }

        if (!apc_store($internalKey, $value, $normalizedOptions['ttl'])) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new Exception\RuntimeException(
                "apc_store('{$internalKey}', <{$type}>, {$normalizedOptions['ttl']}) failed"
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
        return apc_delete($internalKey);
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
        $internalKeys = array();
        $prefix       = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $failedKeys = apc_delete($internalKeys);

        // remove prefix
        $prefixL = strlen($prefix);
        foreach ($failedKeys as & $key) {
            $key = substr($key, $prefixL);
        }

        return $failedKeys;
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
        $value       = (int)$value;
        $newValue    = apc_inc($internalKey, $value);

        // initial value
        if ($newValue === false) {
            $newValue = $value;
            if (!apc_add($internalKey, $newValue, $normalizedOptions['ttl'])) {
                throw new Exception\RuntimeException(
                    "apc_add('{$internalKey}', {$newValue}, {$normalizedOptions['ttl']}) failed"
                );
            }
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
        $value       = (int)$value;
        $newValue    = apc_dec($internalKey, $value);

        // initial value
        if ($newValue === false) {
            // initial value
            $newValue = -$value;
            if (!apc_add($internalKey, $newValue, $normalizedOptions['ttl'])) {
                throw new Exception\RuntimeException(
                    "apc_add('{$internalKey}', {$newValue}, {$normalizedOptions['ttl']}) failed"
                );
            }
        }

        return $newValue;
    }

    /* non-blocking */

    /**
     * Internal method to request multiple items.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-live
     *  - namespace <string>
     *    - The namespace to use
     *  - select <array>
     *    - An array of the information the returned item contains
     *  - callback <callback> optional
     *    - An result callback will be invoked for each item in the result set.
     *    - The first argument will be the item array.
     *    - The callback does not have to return anything.
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    fetch()
     * @see    fetchAll()
     */
    protected function internalGetDelayed(array & $normalizedKeys, array & $normalizedOptions)
    {
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        if (isset($normalizedOptions['callback']) && !is_callable($normalizedOptions['callback'], false)) {
            throw new Exception\InvalidArgumentException('Invalid callback');
        }

        $format = 0;
        foreach ($normalizedOptions['select'] as $property) {
            if (isset(self::$selectMap[$property])) {
                $format = $format | self::$selectMap[$property];
            }
        }

        $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $search = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $search[] = preg_quote($normalizedKey, '/');
        }
        $search = '/^' . preg_quote($prefix, '/') . '(' . implode('|', $search) . ')$/';

        $this->stmtIterator = new APCIterator('user', $search, $format, 1, \APC_LIST_ACTIVE);
        $this->stmtActive   = true;
        $this->stmtOptions  = & $normalizedOptions;

        if (isset($normalizedOptions['callback'])) {
            $callback = & $normalizedOptions['callback'];
            while (($item = $this->fetch()) !== false) {
                call_user_func($callback, $item);
            }
        }

        return true;
    }

    /* find */

    /**
     * internal method to find items.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    fetch()
     * @see    fetchAll()
     */
    protected function internalFind(& $normalizedMode, array & $normalizedOptions)
    {
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        // This adapter doesn't support to read expired items
        if (($normalizedMode & self::MATCH_ACTIVE) == self::MATCH_ACTIVE) {
            $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
            $search = '/^' . preg_quote($prefix, '/') . '+/';

            $format = 0;
            foreach ($normalizedOptions['select'] as $property) {
                if (isset(self::$selectMap[$property])) {
                    $format = $format | self::$selectMap[$property];
                }
            }

            $this->stmtIterator = new APCIterator('user', $search, $format, 1, \APC_LIST_ACTIVE);
            $this->stmtActive   = true;
            $this->stmtOptions  = & $normalizedOptions;
        }

        return true;
    }

    /**
     * Internal method to fetch the next item from result set
     *
     * @return array|boolean The next item or false
     * @throws Exception\ExceptionInterface
     */
    protected function internalFetch()
    {
        if (!$this->stmtActive) {
            return false;
        }

        $prefix  = $this->stmtOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $prefixL = strlen($prefix);

        do {
            if (!$this->stmtIterator->valid()) {
                // clear stmt
                $this->stmtActive   = false;
                $this->stmtIterator = null;
                $this->stmtOptions  = null;

                return false;
            }

            // @see http://pecl.php.net/bugs/bug.php?id=22564
            $exist = apc_exists($this->stmtIterator->key());
            if ($exist) {
                $result = $this->stmtIterator->current();
                $this->normalizeMetadata($result);

                $select = $this->stmtOptions['select'];
                if (in_array('key', $select)) {
                    $internalKey = $this->stmtIterator->key();
                    $result['key'] = substr($internalKey, $prefixL);
                }
            }

            $this->stmtIterator->next();
        } while (!$exist);

        return $result;
    }

    /* cleaning */

    /**
     * Internal method to clear items off all namespaces.
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    clearByNamespace()
     */
    protected function internalClear(& $normalizedMode, array & $normalizedOptions)
    {
        return $this->clearByRegEx('/.*/', $normalizedMode, $normalizedOptions);
    }

    /**
     * Clear items by namespace.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - Tags to search for used with matching modes of Adapter::MATCH_TAGS_*
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    clear()
     */
    protected function internalClearByNamespace(& $normalizedMode, array & $normalizedOptions)
    {
        $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $regex  = '/^' . preg_quote($prefix, '/') . '+/';
        return $this->clearByRegEx($regex, $normalizedMode, $normalizedOptions);
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
                        'atime',
                        'ctime',
                        'internal_key',
                        'mem_size',
                        'mtime',
                        'num_hits',
                        'ref_count',
                        'rtime',
                        'ttl',
                    ),
                    'maxTtl'             => 0,
                    'staticTtl'          => true,
                    'tagging'            => false,
                    'ttlPrecision'       => 1,
                    'useRequestTime'     => (bool) ini_get('apc.use_request_time'),
                    'expiredRead'        => false,
                    'maxKeyLength'       => 5182,
                    'namespaceIsPrefix'  => true,
                    'namespaceSeparator' => $this->getOptions()->getNamespaceSeparator(),
                    'iterable'           => true,
                    'clearAllNamespaces' => true,
                    'clearByNamespace'   => true,
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
        $mem = apc_sma_info(true);
        return array(
            'free'  => $mem['avail_mem'],
            'total' => $mem['num_seg'] * $mem['seg_size'],
        );
    }

    /* internal */

    /**
     * Clear cached items based on key regex
     *
     * @param  string $regex
     * @param  int    $mode
     * @param  array  $options
     * @return bool
     */
    protected function clearByRegEx($regex, $mode, array &$options)
    {
        if (($mode & self::MATCH_ACTIVE) != self::MATCH_ACTIVE) {
            // no need to clear expired items
            return true;
        }

        return apc_delete(new APCIterator('user', $regex, 0, 1, \APC_LIST_ACTIVE));
    }

    /**
     * Normalize metadata to work with APC
     *
     * @param  array $metadata
     * @return void
     */
    protected function normalizeMetadata(array &$metadata)
    {
        // rename
        if (isset($metadata['creation_time'])) {
            $metadata['ctime'] = $metadata['creation_time'];
            unset($metadata['creation_time']);
        }

        if (isset($metadata['access_time'])) {
            $metadata['atime'] = $metadata['access_time'];
            unset($metadata['access_time']);
        }

        if (isset($metadata['deletion_time'])) {
            $metadata['rtime'] = $metadata['deletion_time'];
            unset($metadata['deletion_time']);
        }

        // remove namespace prefix
        if (isset($metadata['key'])) {
            $pos = strpos($metadata['key'], $this->getOptions()->getNamespaceSeparator());
            if ($pos !== false) {
                $metadata['internal_key'] = $metadata['key'];
            } else {
                $metadata['internal_key'] = $metadata['key'];
            }

            unset($metadata['key']);
        }
    }
}
