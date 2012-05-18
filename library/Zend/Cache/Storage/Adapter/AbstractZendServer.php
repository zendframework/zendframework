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
    Zend\Cache\Exception;

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
        $internalKey = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR . $normalizedKey;
        $result      = $this->zdcFetch($internalKey);
        if ($result === false) {
            $success = false;
            $result  = null;
        } else {
            $success  = true;
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
        $prefix = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR;

        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $fetch   = $this->zdcFetchMulti($internalKeys);
        $prefixL = strlen($prefix);
        $result  = array();
        foreach ($fetch as $k => & $v) {
            $result[ substr($k, $prefixL) ] = $v;
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
        $prefix = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR;
        return  ($this->zdcFetch($prefix . $normalizedKey) !== false);
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
        $prefix = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR;

        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $fetch   = $this->zdcFetchMulti($internalKeys);
        $prefixL = strlen($prefix);
        $result  = array();
        foreach ($fetch as $internalKey => & $value) {
            $result[] = substr($internalKey, $prefixL);
        }

        return $result;
    }

    /**
     * Get metadata for multiple items
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
        $prefix       = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR;
        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $fetch   = $this->zdcFetchMulti($internalKeys);
        $prefixL = strlen($prefix);
        $result  = array();
        foreach ($fetch as $internalKey => $value) {
            $result[ substr($internalKey, $prefixL) ] = array();
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
        $internalKey = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR . $normalizedKey;
        $this->zdcStore($internalKey, $value, $normalizedOptions['ttl']);
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
        $internalKey = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR . $normalizedKey;
        return $this->zdcDelete($internalKey);
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
        // clear all
        if (($normalizedMode & self::MATCH_ACTIVE) == self::MATCH_ACTIVE) {
            $this->zdcClear();
        }

        // expired items will be deleted automatic

        return true;
    }

    /**
     * Clear items by namespace.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    clear()
     */
    protected function internalClearByNamespace(& $normalizedMode, array & $normalizedOptions)
    {
        // clear all
        if (($normalizedMode & self::MATCH_ACTIVE) == self::MATCH_ACTIVE) {
            $this->zdcClearByNamespace($normalizedOptions['namespace']);
        }

        // expired items will be deleted automatic

        return true;
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
                $this,
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
                    'staticTtl'          => true,
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
     * @param  string $internalKey
     * @param  mixed  $value
     * @param  int    $ttl
     * @return void
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcStore($internalKey, $value, $ttl);

    /**
     * Fetch a single item from Zend Data Cache (zdc)
     *
     * @param  string $internalKey
     * @return mixed The stored value or FALSE if item wasn't found
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcFetch($internalKey);

    /**
     * Fetch multiple items from Zend Data Cache (zdc)
     *
     * @param  array $internalKeys
     * @return array All found items
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcFetchMulti(array $internalKeys);

    /**
     * Delete data from Zend Data Cache (zdc)
     *
     * @param  string $internalKey
     * @return boolean
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcDelete($internalKey);

    /**
     * Clear items of all namespaces from Zend Data Cache (zdc)
     *
     * @return void
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcClear();

    /**
     * Clear items of the given namespace from Zend Data Cache (zdc)
     *
     * @param  string $namespace
     * @return void
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcClearByNamespace($namespace);
}
