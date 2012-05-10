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
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log;

use Zend\Loader\PluginClassLoader;

/**
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class WriterLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased writers
     */
    protected $plugins = array(
        'db'           => 'Zend\Log\Writer\Db',
        'firebug'      => 'Zend\Log\Writer\Firebug',
        'mail'         => 'Zend\Log\Writer\Mail',
        'mock'         => 'Zend\Log\Writer\Mock',
        'null'         => 'Zend\Log\Writer\Null',
        'stream'       => 'Zend\Log\Writer\Stream',
        'syslog'       => 'Zend\Log\Writer\Syslog',
        'zend_monitor' => 'Zend\Log\Writer\ZendMonitor',
    );
}
