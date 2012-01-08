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

use Zend\Cache\Exception\ExtensionNotLoadedException,
    Zend\Cache\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendServerShm extends AbstractZendServer
{

    public function __construct($options = array())
    {
        if (!function_exists('zend_shm_cache_store')) {
            throw new ExtensionNotLoadedException("Missing 'zend_shm_cache_*' functions");
        } elseif (PHP_SAPI == 'cli') {
            throw new ExtensionNotLoadedException("Zend server data cache isn't available on cli");
        }

        parent::__construct($options);
    }

    public function getCapacity(array $options = array())
    {
        $total = (int)ini_get('zend_datacache.shm.memory_cache_size');
        $total*= 1048576; // MB -> Byte
        return array(
            'total' => $total,
            // TODO: How to get free capacity status
        );
    }

    protected function zdcStore($key, $value, $ttl)
    {
        return zend_shm_cache_store($key, $value, $ttl);
    }

    protected function zdcFetch($key)
    {
        return zend_shm_cache_fetch($key);
    }

    protected function zdcDelete($key)
    {
        return zend_shm_cache_delete($key);
    }

    protected function zdcClear()
    {
        return zend_shm_cache_clear();
    }

    protected function zdcClearByNamespace($namespace)
    {
        return zend_shm_cache_clear($namespace);
    }

}
