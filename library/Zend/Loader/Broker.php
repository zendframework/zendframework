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
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Loader;

/**
 * Broker interface
 *
 * @category   Zend
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Broker
{
    /**
     * Load a plugin and return it
     * 
     * @param  string $plugin 
     * @param  null|array $options Options to pass to the plugin constructor
     * @return object
     */
    public function load($plugin, array $options = null);

    /**
     * Retrieve list of all loaded plugins
     * 
     * @return array
     */
    public function getPlugins();

    /**
     * Whether or not a given plugin has been loaded or registered
     * 
     * @param  string $name 
     * @return bool
     */
    public function isLoaded($name);

    /**
     * Register a named plugin
     * 
     * @param  string $name Name by which plugin will be registered
     * @param  string|object $plugin Plugin class or object
     * @return void
     */
    public function register($name, $plugin);

    /**
     * Unregister a named plugin
     * 
     * @param  string $name 
     * @return void
     */
    public function unregister($name);

    /**
     * Set class loader to use when resolving plugin names to classes
     * 
     * @param  ShortNameLocator $loader 
     * @return void
     */
    public function setClassLoader(ShortNameLocator $loader);

    /**
     * Retrieve the plugin class loader
     * 
     * @return ShortNameLocator
     */
    public function getClassLoader();
}
