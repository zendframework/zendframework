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

namespace Zend\Log;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigurationInterface;

/**
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class WriterPluginManager extends AbstractPluginManager
{
    /**
     * Default set of writers
     * 
     * @var array
     */
    protected $invokableClasses = array(
        'db'          => 'Zend\Log\Writer\Db',
        'firebug'     => 'Zend\Log\Writer\Firebug',
        'mail'        => 'Zend\Log\Writer\Mail',
        'mock'        => 'Zend\Log\Writer\Mock',
        'null'        => 'Zend\Log\Writer\Null',
        'stream'      => 'Zend\Log\Writer\Stream',
        'syslog'      => 'Zend\Log\Writer\Syslog',
        'zendmonitor' => 'Zend\Log\Writer\ZendMonitor',
    );

    /**
     * Validate the plugin
     *
     * Checks that the writer loaded is an instance of Writer\WriterInterface.
     * 
     * @param  mixed $plugin 
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Writer\WriterInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Writer\WriterInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
