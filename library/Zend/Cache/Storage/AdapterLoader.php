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

namespace Zend\Cache\Storage;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for cache storage adapters.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AdapterLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased adapters
     */
    protected $plugins = array(
        'apc'              => 'Zend\Cache\Storage\Adapter\Apc',
        'filesystem'       => 'Zend\Cache\Storage\Adapter\Filesystem',
        'memcached'        => 'Zend\Cache\Storage\Adapter\Memcached',
        'memory'           => 'Zend\Cache\Storage\Adapter\Memory',
        'sysvshm'          => 'Zend\Cache\Storage\Adapter\SystemVShm',
        'systemvshm'       => 'Zend\Cache\Storage\Adapter\SystemVShm',
        'sqlite'           => 'Zend\Cache\Storage\Adapter\Sqlite',
        'dba'              => 'Zend\Cache\Storage\Adapter\Dba',
        'wincache'         => 'Zend\Cache\Storage\Adapter\WinCache',
        'xcache'           => 'Zend\Cache\Storage\Adapter\XCache',
        'zendserverdisk'   => 'Zend\Cache\Storage\Adapter\ZendServerDisk',
        'zend_server_disk' => 'Zend\Cache\Storage\Adapter\ZendServerDisk',
        'zendservershm'    => 'Zend\Cache\Storage\Adapter\ZendServerShm',
        'zend_server_shm'  => 'Zend\Cache\Storage\Adapter\ZendServerShm',
    );
}
