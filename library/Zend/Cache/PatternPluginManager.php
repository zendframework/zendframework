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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for cache pattern adapters
 *
 * Enforces that adatpers retrieved are instances of
 * Pattern\PatternInterface. Additionally, it registers a number of default 
 * patterns available.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PatternPluginManager extends AbstractPluginManager
{
    /**
     * Default set of adapters
     * 
     * @var array
     */
    protected $invokableClasses = array(
        'callback' => 'Zend\Cache\Pattern\CallbackCache',
        'capture'  => 'Zend\Cache\Pattern\CaptureCache',
        'class'    => 'Zend\Cache\Pattern\ClassCache',
        'object'   => 'Zend\Cache\Pattern\ObjectCache',
        'output'   => 'Zend\Cache\Pattern\OutputCache',
        'page'     => 'Zend\Cache\Pattern\PageCache',
    );

    /**
     * Don't share by default
     * 
     * @var array
     */
    protected $shareByDefault = false;

    /**
     * Validate the plugin
     *
     * Checks that the pattern adapter loaded is an instance of Pattern\PatternInterface.
     * 
     * @param  mixed $plugin 
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Pattern\PatternInterface) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Pattern\PatternInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
