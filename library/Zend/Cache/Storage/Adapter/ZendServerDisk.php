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

use Zend\Cache\Utils,
    Zend\Cache\Exception\ExtensionNotLoadedException,
    Zend\Cache\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendServerDisk extends AbstractZendServer
{

    public function __construct($options = array())
    {
        if (!function_exists('zend_disk_cache_store')) {
            throw new ExtensionNotLoadedException("Missing 'zend_disk_cache_*' functions");
        } elseif (PHP_SAPI == 'cli') {
            throw new ExtensionNotLoadedException("Zend server data cache isn't available on cli");
        }

        parent::__construct($options);
    }

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

            $result = Utils::getDiskCapacity(ini_get('zend_datacache.disk.save_path'));
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    protected function zdcStore($key, $value, $ttl)
    {
        return zend_disk_cache_store($key, $value, $ttl);
    }

    protected function zdcFetch($key)
    {
        return zend_disk_cache_fetch($key);
    }

    protected function zdcDelete($key)
    {
        return zend_disk_cache_delete($key);
    }

    protected function zdcClear()
    {
        return zend_disk_cache_clear();
    }

    protected function zdcClearByNamespace($namespace)
    {
        return zend_disk_cache_clear($namespace);
    }

}
