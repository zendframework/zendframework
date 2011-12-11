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
 * @subpackage Zend_Cache_Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Cache\Storage\Adapter;
use Zend\Cache\Storage\Capabilities,
    Zend\Cache\Exception\ExtensionNotLoadedException,
    Zend\Cache\Exception\ItemNotFoundException,
    Zend\Cache\Exception\RuntimeException,
    APCIterator;

/**
 * @uses       \Zend\Cache\Cache
 * @uses       \Zend\Cache\Adapter
 * @uses       \Zend\Cache\Adapter\AbstractAdapter
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @param  array $options Option
     * @throws \Zend\Cache\Exception
     * @return void
     */
    public function __construct($options = array())
    {
        if (version_compare('3.1.6', phpversion('apc')) > 0) {
            throw new ExtensionNotLoadedException("Missing ext/apc >= 3.1.6");
        }

        $enabled = ini_get('apc.enabled');
        if (PHP_SAPI == 'cli') {
            $enabled = $enabled && (bool)ini_get('apc.enable_cli');
        }
        if (!$enabled) {
            throw new ExtensionNotLoadedException(
                "ext/apc is disabled - see 'apc.enabled' and 'apc.enable_cli'"
            );
        }

        // init select map
        if (self::$selectMap === null) {
            self::$selectMap = array(
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

    public function setNamespaceSeparator($separator)
    {
        $this->namespaceSeparator = (string)$separator;
        return $this;
    }

    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /* reading */

    public function getItem($key, array $options = array())
    {
        if (!$this->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $this->getNamespaceSeparator() . $key;

        $value = apc_fetch($key, $success);
        if (!$success) {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException("Key '{$key}' not found");
            }
            return false;
        }

        if (array_key_exists('token', $options)) {
            $options['token'] = $value;
        }

        return $value;
    }

    public function getItems(array $keys, array $options = array())
    {
        if (!$this->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        foreach ($keys as &$key) {
            $key = $options['namespace'] . $this->getNamespaceSeparator() . $key;
        }

        $ret = apc_fetch($keys);

        if (!$options['ignore_missing_items']) {
            if (count($keys) != count($ret)) {
                $missing = implode("', '", array_diff($keys, array_keys($ret)));
                throw new ItemNotFoundException('Keys not found: ' . $missing);
            }
        }

        // remove namespace prefix
        $nsl  = strlen($options['namespace']);
        $ret2 = array();
        foreach ($ret as $key => &$value) {
            $ret2[ substr($key, $nsl+1) ] = $value;
        }

        return $ret2;
    }

    public function hasItem($key, array $options = array())
    {
        if (!$this->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $this->getNamespaceSeparator() . $key;

        return apc_exists($key);
    }

    public function hasItems(array $keys, array $options = array())
    {
        if (!$this->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        foreach ($keys as &$key) {
            $key = $options['namespace'] . $this->getNamespaceSeparator() . $key;
        }

        return apc_exists($keys);
    }

    public function getMetadata($key, array $options = array())
    {
        if (!$this->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $this->getNamespaceSeparator() . $key;

        $format = \APC_ITER_ALL ^ \APC_ITER_VALUE ^ \APC_ITER_TYPE;
        $regexp = '/^' . preg_quote($key, '/') . '$/';
        $it = new APCIterator('user', $regexp, $format, 100, \APC_LIST_ACTIVE);
        $metadata = $it->current();

        // @see http://pecl.php.net/bugs/bug.php?id=22564
        if (!apc_exists($key)) {
            $metadata = false;
        }

        if (!$metadata) {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException(
                    "Key '{$key}' nout found"
                );
            }

            return false;
        }

        $this->normalizeMetadata($metadata);
        return $metadata;
    }

    public function getMetadatas(array $keys, array $options = array())
    {
        if (!$this->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        $nsl = strlen($options['namespace']);

        $keysRegExp = array();
        foreach ($keys as &$key) {
            $keysRegExp[] = preg_quote($options['namespace'] . $this->getNamespaceSeparator() . $key, '/');
        }
        $regexp = '/^(' . implode('|', $keysRegExp) . ')$/';

        $format = \APC_ITER_ALL ^ \APC_ITER_VALUE ^ \APC_ITER_TYPE;

        $it  = new APCIterator('user', $regexp, $format, 100, \APC_LIST_ACTIVE);
        $ret = array();
        foreach ($it as $internalKey => $metadata) {
            // @see http://pecl.php.net/bugs/bug.php?id=22564
            if (!apc_exists($internalKey)) {
                continue;
            }

            $this->normalizeMetadata($metadata);
            $key = substr($internalKey, strpos($internalKey, $this->getNamespaceSeparator()) + 1);
            $ret[$key] = & $metadata;
        }

        if (!$options['ignore_missing_items']) {
            if (count($keys) != count($ret)) {
                $missing = implode("', '", array_diff($keys, array_keys($ret)));
                throw new ItemNotFoundException('Keys not found: ' . $missing);
            }
        }

        return $ret;
    }

    /* writing */

    public function setItem($key, $value, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $this->getNamespaceSeparator() . $key;

        if (!apc_store($key, $value, $options['ttl'])) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new RuntimeException("apc_store('{$key}', <{$type}>, {$options['ttl']}) failed");
        }

        return true;
    }

    public function setItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);

        $keyValuePairs2 = array();
        foreach ($keyValuePairs as $key => &$value) {
            $keyValuePairs2[ $options['namespace'] . $this->getNamespaceSeparator() . $key ] = &$value;
        }

        $errKeys = apc_store($keyValuePairs2, null, $options['ttl']);

        if ($errKeys) {
            throw new RuntimeException(
                "apc_store(<array>, null, {$options['ttl']}) failed for keys: "
                . "'" . implode("','", $errKeys) . "'"
            );
        }

        return true;
    }

    public function addItem($key, $value, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $this->getNamespaceSeparator() . $key;

        if (!apc_add($key, $value, $options['ttl'])) {
            if (apc_exists($key)) {
                throw new RuntimeException("Key '{$key}' already exists");
            }

            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new RuntimeException("apc_add('{$key}', <{$type}>, {$options['ttl']}) failed");
        }

        return true;
    }

    public function addItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);

        $keyValuePairs2 = array();
        foreach ($keyValuePairs as $key => &$value) {
            $keyValuePairs2[ $options['namespace'] . $this->getNamespaceSeparator() . $key ] = &$value;
        }

        $errKeys = apc_add($keyValuePairs2, null, $options['ttl']);

        if ($errKeys) {
            throw new RuntimeException(
                "apc_add(<array>, null, {$options['ttl']}) failed for keys: "
                . "'" . implode("','", $errKeys) . "'"
            );
        }

        return true;
    }

    public function replaceItem($key, $value, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $this->getNamespaceSeparator() . $key;

        if (!apc_exists($key)) {
            throw new ItemNotFoundException("Key '{$key}' doesn't exist");
        }

        if (!apc_store($key, $value, $options['ttl'])) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new RuntimeException("apc_store('{$key}', <{$type}>, {$options['ttl']}) failed");
        }

        return true;
    }

    public function removeItem($key, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $this->getNamespaceSeparator() . $key;

        if (!apc_delete($key)) {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException("Key '{$key}' not found");
            }
        }

        return true;
    }

    public function removeItems(array $keys, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        foreach ($keys as &$key) {
            $key = $options['namespace'] . $this->getNamespaceSeparator() . $key;
        }

        $errKeys = apc_delete($keys);
        if ($errKeys) {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException("Keys '" . implode("','", $errKeys) . "' not found");
            }
        }

        return true;
    }

    public function incrementItem($key, $value, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $internalKey = $options['namespace'] . $this->getNamespaceSeparator() . $key;

        $value = (int)$value;
        $newValue = apc_inc($internalKey, $value);
        if ($newValue === false) {
            if (!apc_exists($internalKey)) {
                if ($options['ignore_missing_items']) {
                    $this->addItem($key, $value, $options);
                    $newValue = $value;
                } else {
                    throw new ItemNotFoundException(
                        "Key '{$internalKey}' not found"
                    );
                }
            } else {
                throw new RuntimeException("apc_inc('{$internalKey}', {$value}) failed");
            }
        }

        return $newValue;
    }

    public function decrementItem($key, $value, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $internalKey = $options['namespace'] . $this->getNamespaceSeparator() . $key;

        $value = (int)$value;
        $newValue = apc_dec($internalKey, $value);
        if ($newValue === false) {
            if (!apc_exists($internalKey)) {
                if ($options['ignore_missing_items']) {
                    $this->addItem($key, -$value, $options);
                    $newValue = -$value;
                } else {
                    throw new ItemNotFoundException(
                        "Key '{$internalKey}' not found"
                    );
                }
            } else {
                throw new RuntimeException("apc_inc('{$internalKey}', {$value}) failed");
            }
        }

        return $newValue;
    }

    /* non-blocking */

    public function getDelayed(array $keys, array $options = array())
    {
        if ($this->stmtActive) {
            throw new RuntimeException('Statement already in use');
        }

        if (!$this->getReadable()) {
            return false;
        }

        if (!$keys) {
            return true;
        }

        $this->normalizeOptions($options);

        $prefix = $options['namespace'] . $this->getNamespaceSeparator();
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
            if (!is_callable($callback, false)) {
                $this->stmtActive   = false;
                $this->stmtIterator = null;
                $this->stmtOptions  = null;
                throw new InvalidArgumentException('Invalid callback');
            }

            while ( ($item = $this->fetch()) !== false) {
                call_user_func($callback, $item);
            }
        }

        return true;
    }

    public function find($mode = self::MATCH_ACTIVE, array $options = array())
    {
        if ($this->stmtActive) {
            throw new RuntimeException('Statement already in use');
        }

        if (!$this->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_ACTIVE, $options);
        if (($mode & self::MATCH_ACTIVE) != self::MATCH_ACTIVE) {
            // This adapter doen't support to read expired items
            return true;
        }

        $prefix = $options['namespace'] . $this->getNamespaceSeparator();
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

        return true;
    }

    public function fetch()
    {
        if (!$this->stmtActive) {
            return false;
        }

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
                $metadata = $this->stmtIterator->current();
                $this->normalizeMetadata($metadata);

                $select = $this->stmtOptions['select'];
                if (in_array('key', $select)) {
                    $internalKey = $this->stmtIterator->key();
                    $key = substr($internalKey, strpos($internalKey, $this->getNamespaceSeparator()) + 1);
                    $metadata['key'] = $key;
                }
            }

            $this->stmtIterator->next();

        } while (!$exist);

        return $metadata;
    }

    /* cleaning */

    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
        $this->normalizeOptions($options);
        return $this->clearByRegEx('/.*/', $mode, $options);
    }

    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
        $this->normalizeOptions($options);
        $prefix = $options['namespace'] . $this->getNamespaceSeparator();
        $regex  = '/^' . preg_quote($prefix, '/') . '+/';

        return $this->clearByRegEx($regex, $mode, $options);
    }

    /* status */

    public function getCapabilities()
    {
        if ($this->capabilities === null) {
            $this->capabilityMarker = new \stdClass();
            $this->capabilities = new Capabilities(
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
                        'resource' => false
                    ),
                    'supportedMetadata' => array(
                        'mtime', 'ctime', 'atime', 'rtime', 'ttl',
                        'num_hits', 'ref_count', 'mem_size', 'internal_key'
                    ),
                    'maxTtl'             => 0,
                    'staticTtl'          => false,
                    'ttlPrecision'       => 1,
                    'useRequestTime'     => (bool)ini_get('apc.use_request_time'),
                    'expiredRead'        => false,
                    'maxKeyLength'       => 5182,
                    'namespaceIsPrefix'  => true,
                    'namespaceSeparator' => $this->getNamespaceSeparator(),
                    'iterable'           => true,
                    'clearAllNamespaces' => true,
                    'clearByNamespace'   => true,
                )
            );
        }

        return $this->capabilities;
    }

    public function getCapacity(array $options = array())
    {
        $mem = apc_sma_info(true);

        return array(
            'free'  => $mem['avail_mem'],
            'total' => $mem['num_seg'] * $mem['seg_size'],
        );
    }

    /* internal */

    protected function clearByRegEx($regex, $mode, array &$options)
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);
        if (($mode & self::MATCH_ACTIVE) != self::MATCH_ACTIVE) {
            // no need to clear expired items
            return true;
        }

        return apc_delete(new APCIterator('user', $regex, 0, 1, \APC_LIST_ACTIVE));
    }

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
            $pos = strpos($metadata['key'], $this->getNamespaceSeparator());
            if ($pos !== false) {
                $metadata['internal_key'] = $metadata['key'];
            } else {
                $metadata['internal_key'] = $metadata['key'];
            }

            unset($metadata['key']);
        }
    }

}
