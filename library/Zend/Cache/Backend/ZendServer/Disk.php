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
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Cache\Backend\ZendServer;
use Zend\Cache,
    Zend\Cache\Backend;

/**
 * @uses       \Zend\Cache\Cache
 * @uses       \Zend\Cache\Backend
 * @uses       \Zend\Cache\Backend\ZendServer\AbstractZendServer
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Disk extends AbstractZendServer implements Backend
{
    /**
     * Constructor
     *
     * @param  array $options associative array of options
     * @throws \Zend\Cache\Exception
     */
    public function __construct(array $options = array())
    {
        if (!function_exists('zend_disk_cache_store')) {
            Cache\Cache::throwException('Zend_Cache_ZendServer_Disk backend has to be used within Zend Server environment.');
        }
        parent::__construct($options);
    }

    /**
     * Store data
     *
     * @param mixed  $data        Object to store
     * @param string $id          Cache id
     * @param int    $timeToLive  Time to live in seconds
     * @return boolean true if no problem
     */
    protected function _store($data, $id, $timeToLive)
    {
        if (zend_disk_cache_store($this->_options['namespace'] . '::' . $id,
                                  $data,
                                  $timeToLive) === false) {
            $this->_log('Store operation failed.');
            return false;
        }
        return true;
    }

    /**
     * Fetch data
     *
     * @param string $id          Cache id
     */
    protected function _fetch($id)
    {
        return zend_disk_cache_fetch($this->_options['namespace'] . '::' . $id);
    }

    /**
     * Unset data
     *
     * @param string $id          Cache id
     * @return boolean true if no problem
     */
    protected function _unset($id)
    {
        return zend_disk_cache_delete($this->_options['namespace'] . '::' . $id);
    }

    /**
     * Clear cache
     */
    protected function _clear()
    {
        zend_disk_cache_clear($this->_options['namespace']);
    }
}
