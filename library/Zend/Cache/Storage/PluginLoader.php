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
 * Plugin Class Loader implementation for cache storage plugins.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PluginLoader extends PluginClassLoader
{
    /**
     * Pre-aliased adapters
     *
     * @var array
     */
    protected $plugins = array(
        'clear_by_factor'    => 'Zend\Cache\Storage\Plugin\ClearByFactor',
        'clearbyfactor'      => 'Zend\Cache\Storage\Plugin\ClearByFactor',
        'exception_handler'  => 'Zend\Cache\Storage\Plugin\ExceptionHandler',
        'exceptionhandler'   => 'Zend\Cache\Storage\Plugin\ExceptionHandler',
        //'filter'             => 'Zend\Cache\Storage\Plugin\Filter',
        'ignore_user_abort'  => 'Zend\Cache\Storage\Plugin\IgnoreUserAbort',
        'ignoreuserabort'    => 'Zend\Cache\Storage\Plugin\IgnoreUserAbort',
        //'key_filter'         => 'Zend\Cache\Storage\Plugin\KeyFilter',
        //'keyfilter'          => 'Zend\Cache\Storage\Plugin\KeyFilter',
        //'levels'             => 'Zend\Cache\Storage\Plugin\Levels',
        //'locking'            => 'Zend\Cache\Storage\Plugin\Locking',
        //'master_file'        => 'Zend\Cache\Storage\Plugin\MasterFile',
        //'masterfile'         => 'Zend\Cache\Storage\Plugin\MasterFile',
        'optimize_by_factor' => 'Zend\Cache\Storage\Plugin\OptimizeByFactor',
        'optimizebyfactor'   => 'Zend\Cache\Storage\Plugin\OptimizeByFactor',
        //'reluctant'          => 'Zend\Cache\Storage\Plugin\Reluctant',
        'serializer'         => 'Zend\Cache\Storage\Plugin\Serializer',
        //'store_times'        => 'Zend\Cache\Storage\Plugin\StoreTimes',
        //'storetimes'         => 'Zend\Cache\Storage\Plugin\StoreTimes',
        //'tagging'            => 'Zend\Cache\Storage\Plugin\Tagging',
        //'write_control'      => 'Zend\Cache\Storage\Plugin\WriteControl',
        //'writecontrol'       => 'Zend\Cache\Storage\Plugin\WriteControl',
    );
}
