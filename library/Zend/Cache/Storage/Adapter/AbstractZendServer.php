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

use Zend\Cache,
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

    const NAMESPACE_SEPARATOR = '::';

    /* reading */

    public function getItem($key, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);

        $key = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;

        $rs = $this->zdcFetch($key);
        if ($rs === false) {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException("Key '{$key}' not found");
            }

            $rs = false;
        }

        if (array_key_exists('token', $options)) {
            $options['token'] = $rs;
        }

        return $rs;
    }

    public function getItems(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        foreach ($keys as &$key) {
            $this->normalizeKey($key);
            $key = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
        }

        $rs = $this->zdcFetch($keys);
        if ($rs === false) {
            throw new RuntimeException("Failed to fetch keys");
        }

        // remove namespace
        $nsl = strlen($options['namespace']) + strlen(self::NAMESPACE_SEPARATOR);
        $rsItems = array();
        foreach ($rs as $k => &$v) {
            $rsItems[ substr($k, $nsl) ] = $v;
        }

        return $rsItems;
    }

    public function hasItem($key, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $key = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;

        return ($this->zdcFetch($key) !== false);
    }

    public function hasItems(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        foreach ($keys as &$key) {
            $this->normalizeKey($key);
            $key = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
        }

        $rs = $this->zdcFetch($keys);
        if ($rs === false) {
            throw new RuntimeException("Failed to fetch keys");
        }

        $rsExists = array();
        $nsl = strlen($options['namespace']) + strlen(self::NAMESPACE_SEPARATOR);
        foreach ($rs as $k => $v) {
            $k = substr($k, $nsl);
            $rsExists[$k] = true;
        }

        return $rsExists;
    }

    public function getMetadata($key, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $key = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;

        $rs = $this->zdcFetch($key);
        if ($rs === false) {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException("Key '{$key}' not found");
            }

            return false;
        }

        return array();
    }

    public function getMetadatas(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        foreach ($keys as &$key) {
            $this->normalizeKey($key);
            $key = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
        }

        $rs = $this->zdcFetch($keys);
        if ($rs === false) {
            throw new RuntimeException("Failed to fetch keys");
        }

        $rsInfo = array();
        $nsl = strlen($options['namespace']) + strlen(self::NAMESPACE_SEPARATOR);
        foreach ($rs as $k => $v) {
            $k = substr($k, $nsl);
            $rsInfo[$k] = array();
        }

        return $rsInfo;
    }

    /* writing */

    public function setItem($key, $value, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $key = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;

        return $this->zdcStore($key, $value, $options['ttl']);
    }

    public function removeItem($key, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $key = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;

        if (!$this->zdcDelete($key)) {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException("Key '{$key}' not found");
            }
        }

        return true;
    }

    /* cleaning */

    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);

        // clear all
        if (($mode & self::MATCH_ACTIVE) == self::MATCH_ACTIVE) {
            $rs = $this->zdcClear();
            if ($rs === false) {
                throw new RuntimeException("Clearing failed");
            }
        }

        // expired items will be deleted automatic

        return true;
    }

    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);

        // clear all
        if (($mode & self::MATCH_ACTIVE) == self::MATCH_ACTIVE) {
            $rs = $this->zdcClearByNamespace($options['namespace']);
            if ($rs === false) {
                throw new RuntimeException("Clearing failed");
            }
        }

        // expired items will be deleted automatic

        return true;
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

        return $this->capabilities;
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
