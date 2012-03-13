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
     * @throws Exception
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

        $this->options = $options;
        return $this;
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
     * Get an item.
     *
     * Options:
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
            $result      = apc_fetch($internalKey, $success);
            if (!$success) {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
                }
                $result = false;
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

            $fetch = apc_fetch($internalKeys);
            if (!$options['ignore_missing_items']) {
                if (count($keys) != count($fetch)) {
                    $missing = implode("', '", array_diff($internalKeys, array_keys($fetch)));
                    throw new Exception\ItemNotFoundException('Keys not found: ' . $missing);
                }
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
     * Test if an item exists.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers hasItem.pre(PreEvent)
     * @triggers hasItem.post(PostEvent)
     * @triggers hasItem.exception(ExceptionEvent)
     */
    public function hasItem($key, array $options = array())
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
            $result      = apc_exists($internalKey);

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Test if multiple items exists.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers hasItems.pre(PreEvent)
     * @triggers hasItems.post(PostEvent)
     * @triggers hasItems.exception(ExceptionEvent)
     */
    public function hasItems(array $keys, array $options = array())
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

            $exists  = apc_exists($internalKeys);
            $result  = array();
            $prefixL = strlen($options['namespace'] . $namespaceSep);
            foreach ($exists as $internalKey => $bool) {
                if ($bool === true) {
                    $result[] = substr($internalKey, $prefixL);
                }
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

            $format   = \APC_ITER_ALL ^ \APC_ITER_VALUE ^ \APC_ITER_TYPE;
            $regexp   = '/^' . preg_quote($internalKey, '/') . '$/';
            $it       = new APCIterator('user', $regexp, $format, 100, \APC_LIST_ACTIVE);
            $metadata = $it->current();

            // @see http://pecl.php.net/bugs/bug.php?id=22564
            if (!apc_exists($internalKey)) {
                $metadata = false;
            }

            if (!$metadata) {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
                }
            } else {
                $this->normalizeMetadata($metadata);
            }

            return $this->triggerPost(__FUNCTION__, $args, $metadata);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Get metadata of multiple items
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return array
     * @throws Exception\ItemNotFoundException
     *
     * @triggers getMetadatas.pre(PreEvent)
     * @triggers getMetadatas.post(PostEvent)
     * @triggers getMetadatas.exception(ExceptionEvent)
     */
    public function getMetadatas(array $keys, array $options = array())
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

            $keysRegExp = array();
            foreach ($keys as $key) {
                $keysRegExp[] = preg_quote($key, '/');
            }
            $regexp = '/^'
                . preg_quote($options['namespace'] . $baseOptions->getNamespaceSeparator(), '/')
                . '(' . implode('|', $keysRegExp) . ')'
                . '$/';
            $format = \APC_ITER_ALL ^ \APC_ITER_VALUE ^ \APC_ITER_TYPE;

            $it      = new APCIterator('user', $regexp, $format, 100, \APC_LIST_ACTIVE);
            $result  = array();
            $prefixL = strlen($options['namespace'] . $baseOptions->getNamespaceSeparator());
            foreach ($it as $internalKey => $metadata) {
                // @see http://pecl.php.net/bugs/bug.php?id=22564
                if (!apc_exists($internalKey)) {
                    continue;
                }

                $this->normalizeMetadata($metadata);
                $result[ substr($internalKey, $prefixL) ] = & $metadata;
            }

            if (!$options['ignore_missing_items']) {
                if (count($keys) != count($result)) {
                    $missing = implode("', '", array_diff($keys, array_keys($result)));
                    throw new Exception\ItemNotFoundException('Keys not found: ' . $missing);
                }
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* writing */

    /**
     * Store an item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
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
            if (!apc_store($internalKey, $value, $options['ttl'])) {
                $type = is_object($value) ? get_class($value) : gettype($value);
                throw new Exception\RuntimeException(
                    "apc_store('{$internalKey}', <{$type}>, {$options['ttl']}) failed"
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
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
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

            $errKeys = apc_store($internalKeyValuePairs, null, $options['ttl']);
            if ($errKeys) {
                throw new Exception\RuntimeException(
                    "apc_store(<array>, null, {$options['ttl']}) failed for keys: "
                    . "'" . implode("','", $errKeys) . "'"
                );
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
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
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
            if (!apc_add($internalKey, $value, $options['ttl'])) {
                if (apc_exists($internalKey)) {
                    throw new Exception\RuntimeException("Key '{$internalKey}' already exists");
                }

                $type = is_object($value) ? get_class($value) : gettype($value);
                throw new Exception\RuntimeException(
                    "apc_add('{$internalKey}', <{$type}>, {$options['ttl']}) failed"
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
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
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

            $errKeys = apc_add($internalKeyValuePairs, null, $options['ttl']);
            if ($errKeys) {
                throw new Exception\RuntimeException(
                    "apc_add(<array>, null, {$options['ttl']}) failed for keys: "
                    . "'" . implode("','", $errKeys) . "'"
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
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
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
            if (!apc_exists($internalKey)) {
                throw new Exception\ItemNotFoundException(
                    "Key '{$internalKey}' doesn't exist"
                );
            }

            if (!apc_store($internalKey, $value, $options['ttl'])) {
                $type = is_object($value) ? get_class($value) : gettype($value);
                throw new Exception\RuntimeException(
                    "apc_store('{$internalKey}', <{$type}>, {$options['ttl']}) failed"
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
            if (!apc_delete($internalKey)) {
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
     * Remove multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  array $keys
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers removeItems.pre(PreEvent)
     * @triggers removeItems.post(PostEvent)
     * @triggers removeItems.exception(ExceptionEvent)
     */
    public function removeItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
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

            $internalKeys = array();
            $prefix       = $options['namespace'] . $baseOptions->getNamespaceSeparator();
            foreach ($keys as $key) {
                $internalKeys[] = $prefix . $key;
            }

            $errKeys = apc_delete($internalKeys);
            if ($errKeys) {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException("Keys '" . implode("','", $errKeys) . "' not found");
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
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
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
            $newValue    = apc_inc($internalKey, $value);
            if ($newValue === false) {
                if (apc_exists($internalKey)) {
                    throw new Exception\RuntimeException("apc_inc('{$internalKey}', {$value}) failed");
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
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
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
            $newValue    = apc_dec($internalKey, $value);
            if ($newValue === false) {
                if (apc_exists($internalKey)) {
                    throw new Exception\RuntimeException("apc_dec('{$internalKey}', {$value}) failed");
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

    /**
     * Get items that were marked to delay storage for purposes of removing blocking
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return bool
     * @throws Exception
     *
     * @triggers getDelayed.pre(PreEvent)
     * @triggers getDelayed.post(PostEvent)
     * @triggers getDelayed.exception(ExceptionEvent)
     */
    public function getDelayed(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        } elseif (!$baseOptions->getReadable()) {
            return false;
        } elseif (!$keys) {
            return true;
        }

        $this->normalizeOptions($options);
        if (isset($options['callback']) && !is_callable($options['callback'], false)) {
            throw new Exception\InvalidArgumentException('Invalid callback');
        }

        $args = new ArrayObject(array(
            'keys'    => & $keys,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $prefix = $options['namespace'] . $baseOptions->getNamespaceSeparator();
            $prefix = preg_quote($prefix, '/');

            $format = 0;
            foreach ($options['select'] as $property) {
                if (isset(self::$selectMap[$property])) {
                    $format = $format | self::$selectMap[$property];
                }
            }

            $search = array();
            foreach ($keys as $key) {
                $search[] = preg_quote($key, '/');
            }
            $search = '/^' . $prefix . '(' . implode('|', $search) . ')$/';

            $this->stmtIterator = new APCIterator('user', $search, $format, 1, \APC_LIST_ACTIVE);
            $this->stmtActive   = true;
            $this->stmtOptions  = &$options;

            if (isset($options['callback'])) {
                $callback = $options['callback'];
                while (($item = $this->fetch()) !== false) {
                    call_user_func($callback, $item);
                }
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Find items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Exception
     * @see fetch()
     * @see fetchAll()
     *
     * @triggers find.pre(PreEvent)
     * @triggers find.post(PostEvent)
     * @triggers find.exception(ExceptionEvent)
     */
    public function find($mode = self::MATCH_ACTIVE, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        } elseif (!$baseOptions->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_ACTIVE, $options);
        $args = new ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            // This adapter doesn't support to read expired items
            if (($mode & self::MATCH_ACTIVE) == self::MATCH_ACTIVE) {
                $prefix = $options['namespace'] . $baseOptions->getNamespaceSeparator();
                $search = '/^' . preg_quote($prefix, '/') . '+/';

                $format = 0;
                foreach ($options['select'] as $property) {
                    if (isset(self::$selectMap[$property])) {
                        $format = $format | self::$selectMap[$property];
                    }
                }

                $this->stmtIterator = new APCIterator('user', $search, $format, 1, \APC_LIST_ACTIVE);
                $this->stmtActive   = true;
                $this->stmtOptions  = &$options;
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Fetches the next item from result set
     *
     * @return array|boolean The next item or false
     * @see    fetchAll()
     *
     * @triggers fetch.pre(PreEvent)
     * @triggers fetch.post(PostEvent)
     * @triggers fetch.exception(ExceptionEvent)
     */
    public function fetch()
    {
        if (!$this->stmtActive) {
            return false;
        }

        $args = new ArrayObject();

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $prefixL = strlen($this->stmtOptions['namespace'] . $this->getOptions()->getNamespaceSeparator());

            do {
                if (!$this->stmtIterator->valid()) {
                    // clear stmt
                    $this->stmtActive   = false;
                    $this->stmtIterator = null;
                    $this->stmtOptions  = null;

                    $result = false;
                    break;
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

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* cleaning */

    /**
     * Clear items off all namespaces.
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

            $result = $this->clearByRegEx('/.*/', $mode, $options);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Clear items by namespace.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     * @see clear()
     *
     * @triggers clearByNamespace.pre(PreEvent)
     * @triggers clearByNamespace.post(PostEvent)
     * @triggers clearByNamespace.exception(ExceptionEvent)
     */
    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
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

            $prefix = $options['namespace'] . $baseOptions->getNamespaceSeparator();
            $regex  = '/^' . preg_quote($prefix, '/') . '+/';
            $result = $this->clearByRegEx($regex, $mode, $options);
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

            $mem    = apc_sma_info(true);
            $result = array(
                'free'  => $mem['avail_mem'],
                'total' => $mem['num_seg'] * $mem['seg_size'],
            );
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
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
