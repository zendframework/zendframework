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
    Zend\Cache\Storage\Capabilities,
    Zend\Cache\Exception\RuntimeException,
    Zend\Cache\Exception\ItemNotFoundException;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractZendServer extends AbstractAdapter
{
    /**
     * The namespace separator used on Zend Data Cache functions
     *
     * @var string
     */
    const NAMESPACE_SEPARATOR = '::';

    /* reading */

    public function getItem($key, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
            $result      = $this->zdcFetch($internalKey);
            if ($result === false) {
                if (!$options['ignore_missing_items']) {
                    throw new ItemNotFoundException("Key '{$internalKey}' not found");
                }

                $result = false;
            } elseif (array_key_exists('token', $options)) {
                $options['token'] = $result;
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    public function getItems(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
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

            $internalKeys = array();
            foreach ($keys as &$key) {
                $this->normalizeKey($key);
                $internalKeys[] = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
            }

            $fetch = $this->zdcFetch($internalKeys);
            if ($fetch === false) {
                throw new RuntimeException("Failed to fetch keys");
            }

            // remove namespace
            $prefixL = strlen($options['namespace'] . self::NAMESPACE_SEPARATOR);
            $result  = array();
            foreach ($fetch as $k => &$v) {
                $result[ substr($k, $prefixL) ] = $v;
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    public function hasItem($key, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
            $result      = ($this->zdcFetch($internalKey) !== false);

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    public function hasItems(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
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

            $internalKeys = array();
            foreach ($keys as &$key) {
                $this->normalizeKey($key);
                $internalKeys[] = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
            }

            $fetch = $this->zdcFetch($internalKeys);
            if ($fetch === false) {
                throw new RuntimeException("Failed to fetch keys");
            }

            $prefixL = strlen($options['namespace'] . self::NAMESPACE_SEPARATOR);
            $result  = array();
            foreach ($fetch as $k => &$v) {
                $result[] = substr($k, $prefixL);
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    public function getMetadata($key, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
            $result      = ($this->zdcFetch($internalKey) !== false) ? array() : false;

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    public function getMetadatas(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
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

            $internalKeys = array();
            foreach ($keys as &$key) {
                $this->normalizeKey($key);
                $internalKeys[] = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
            }

            $fetch = $this->zdcFetch($internalKeys);
            if ($fetch === false) {
                throw new RuntimeException("Failed to fetch keys");
            }

            $prefixL = strlen($options['namespace'] . self::NAMESPACE_SEPARATOR);
            $result  = array();
            foreach ($fetch as $k => &$v) {
                $result[ substr($k, $prefixL) ] = array();
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* writing */

    public function setItem($key, $value, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
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

            $internalKey = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
            if (!$this->zdcStore($internalKey, $value, $options['ttl'])) {
                throw new RuntimeException(
                    "zend_xxx_cache_store($internalKey, <value>, {$options['ttl']}) failed"
                );
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    public function removeItem($key, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
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

            $internalKey = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
            if (!$this->zdcDelete($internalKey) && !$options['ignore_missing_items']) {
                throw new ItemNotFoundException("Key '{$internalKey}' not found");
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* cleaning */

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

            // clear all
            if (($mode & self::MATCH_ACTIVE) == self::MATCH_ACTIVE) {
                if (!$this->zdcClear()) {
                    throw new RuntimeException("Clearing failed");
                }
            }

            // expired items will be deleted automatic

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
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

            // clear all
            if (($mode & self::MATCH_ACTIVE) == self::MATCH_ACTIVE) {
                $rs = $this->zdcClearByNamespace($options['namespace']);
                if ($rs === false) {
                    throw new RuntimeException("Clearing failed");
                }
            }

            // expired items will be deleted automatic

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* status */

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
                        'supportedMetadata'  => array(),
                        'maxTtl'             => 0,
                        'staticTtl'          => false,
                        'tagging'            => false,
                        'ttlPrecision'       => 1,
                        'useRequestTime'     => false,
                        'expiredRead'        => false,
                        'maxKeyLength'       => 0,
                        'namespaceIsPrefix'  => true,
                        'namespaceSeparator' => self::NAMESPACE_SEPARATOR,
                        'iterable'           => false,
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

    /* internal wrapper of zend_[disk|shm]_cache_* functions */

    /**
     * Store data into Zend Data Cache (zdc)
     *
     * @param string $internalKey
     * @param mixed  $value
     * @param int    $ttl
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    abstract protected function zdcStore($internalKey, $value, $ttl);

    /**
     * Fetch data from Zend Data Cache (zdc)
     *
     * @param string $internalKey
     * @return mixed The stored value or FALSE if item wasn't found
     * @throws Zend\Cache\Exception
     */
    abstract protected function zdcFetch($internalKey);

    /**
     * Delete data from Zend Data Cache (zdc)
     *
     * @param string $internalKey
     * @return boolean
     */
    abstract protected function zdcDelete($internalKey);

    /**
     * Clear items of all namespaces from Zend Data Cache (zdc)
     *
     * @return boolean
     */
    abstract protected function zdcClear();

    /**
     * Clear items of the given namespace from Zend Data Cache (zdc)
     *
     * @param string $namespace
     * @return boolean
     */
    abstract protected function zdcClearByNamespace($namespace);
}
