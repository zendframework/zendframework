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
 */

namespace Zend\Loader;

/**
 * Lazy-loading broker interface
 *
 * @category   Zend
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface LazyLoadingBroker extends Broker
{
    /**
     * Register a plugin specification
     *
     * Registers a plugin "specification". Implementations should allow
     * aggregating such specifications in order to retrieve "registered" 
     * plugins later. The specification will be the argument passed to 
     * load() when the plugin is requested later.
     * 
     * @param  string $name 
     * @param  array $spec 
     * @return LazyLoadingBroker
     */
    public function registerSpec($name, array $spec = null);

    /**
     * Register many plugin specifications at once
     *
     * Implementations should allow both array and Traversable arguments, and
     * loop through the argument assuming key/value pairs of name/specs.
     * 
     * @param  array|Traversable $specs 
     * @return LazyLoadingBroker
     */
    public function registerSpecs($specs);

    /**
     * Unregister a plugin specification
     * 
     * @param  string $name 
     * @return void
     */
    public function unregisterSpec($name);

    /**
     * Retrieve a list of plugins and/or specs registered
     *
     * Differs from getPlugins() in that this will return true for both a 
     * plugin that has been loaded, as well as a plugin for which only a spec
     * is available.
     * 
     * @return array
     */
    public function getRegisteredPlugins();

    /**
     * Whether or not a plugin exists
     *
     * Should be used to indicate either whether a given plugin has been 
     * previously loaded, or whether a specification has been registered.
     * As such, it differs from isLoaded(), which should report only if the
     * plugin has already been loaded.
     * 
     * @param  string $name 
     * @return bool
     */
    public function hasPlugin($name);
}
