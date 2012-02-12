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
    Zend\Cache\Exception;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendServerShm extends AbstractZendServer
{

    /**
     * Constructor
     *
     * @param  null|array|Traversable|AdapterOptions $options
     * @throws Exception
     * @return void
     */
    public function __construct($options = array())
    {
        if (!function_exists('zend_shm_cache_store')) {
            throw new Exception\ExtensionNotLoadedException("Missing 'zend_shm_cache_*' functions");
        } elseif (PHP_SAPI == 'cli') {
            throw new Exception\ExtensionNotLoadedException("Zend server data cache isn't available on cli");
        }

        parent::__construct($options);
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

            $total = (int)ini_get('zend_datacache.shm.memory_cache_size');
            $total*= 1048576; // MB -> Byte
            $result = array(
                'total' => $total,
                // TODO: How to get free capacity status
            );

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Store data into Zend Data SHM Cache
     *
     * @param  string $internalKey
     * @param  mixed  $value
     * @param  int    $ttl
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function zdcStore($internalKey, $value, $ttl)
    {
        if (!zend_shm_cache_store($internalKey, $value, $ttl)) {
            $valueType = gettype($value);
            throw new Exception\RuntimeException(
                "zend_disk_cache_store($internalKey, <{$valueType}>, {$ttl}) failed"
            );
        }
    }

    /**
     * Fetch a single item from Zend Data SHM Cache
     *
     * @param  string $internalKey
     * @return mixed The stored value or FALSE if item wasn't found
     * @throws Exception\RuntimeException
     */
    protected function zdcFetch($internalKey)
    {
        return zend_shm_cache_fetch((string)$internalKey);
    }

    /**
     * Fetch multiple items from Zend Data SHM Cache
     *
     * @param  array $internalKeys
     * @return array All found items
     * @throws Exception\RuntimeException
     */
    protected function zdcFetchMulti(array $internalKeys)
    {
        $items = zend_shm_cache_fetch($internalKeys);
        if ($items === false) {
            throw new Exception\RuntimeException("zend_shm_cache_fetch(<array>) failed");
        }
        return $items;
    }

    /**
     * Delete data from Zend Data SHM Cache
     *
     * @param  string $internalKey
     * @return boolean
     * @throws Exception\RuntimeException
     */
    protected function zdcDelete($internalKey)
    {
        return zend_shm_cache_delete($internalKey);
    }

    /**
     * Clear items of all namespaces from Zend Data SHM Cache
     *
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function zdcClear()
    {
        if (!zend_shm_cache_clear()) {
            throw new Exception\RuntimeException(
                'zend_shm_cache_clear() failed'
            );
        }
    }

    /**
     * Clear items of the given namespace from Zend Data SHM Cache
     *
     * @param  string $namespace
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function zdcClearByNamespace($namespace)
    {
        if (!zend_shm_cache_clear($namespace)) {
            throw new Exception\RuntimeException(
                "zend_shm_cache_clear({$namespace}) failed"
            );
        }
    }
}
