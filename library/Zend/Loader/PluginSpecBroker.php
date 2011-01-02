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
 * Lazy-loading plugin broker
 *
 * @category   Zend
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PluginSpecBroker extends PluginBroker implements LazyLoadingBroker
{
    /**
     * Registered specifications
     * 
     * @var array
     */
    protected $specs = array();

    /**
     * Set object state
     *
     * Allows configuration of broker. Options are first passed to parent 
     * method, and then scanned for a "specs" key; if found, it is used to
     * configure plugin specifications.
     * 
     * @param  array|Traversable $options 
     * @return PluginSpecBroker
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        foreach ($options as $key => $value) {
            switch(strtolower($key)) {
                case 'specs':
                    if (!is_array($value) && !$value instanceof \Traversable) {
                        throw new Exception\RuntimeException(sprintf(
                            'Expected array or Traversable for specs option; received "%s"',
                            (is_object($value) ? get_class($value) : gettype($value))
                        ));
                    }
                    foreach ($value as $name => $pluginOptions) {
                        $this->registerSpec($name, $pluginOptions);
                    }
                    break;
                default:
                    // ignore unknown options
                    break;
            }
        }

        return $this;
    }

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
     * @return PluginSpecBroker
     */
    public function registerSpec($name, array $spec = null)
    {
        $name = strtolower($name);
        $this->specs[$name] = $spec;
        return $this;
    }

    /**
     * Register a plugin
     *
     * Overrides parent method to allow passing array or null value for 
     * $plugin; if so, it proxies to {@link registerSpec()}.
     * 
     * @param  string $name 
     * @param  mixed $plugin 
     * @return PluginSpecBroker
     */
    public function register($name, $plugin)
    {
        if (null === $plugin || is_array($plugin)) {
            $this->registerSpec($name, $plugin);
            return $this;
        }
        return parent::register($name, $plugin);
    }

    /**
     * Register many plugin specifications at once
     *
     * Implementations should allow both array and Traversable arguments, and
     * loop through the argument assuming key/value pairs of name/specs.
     * 
     * @param  array|Traversable $specs 
     * @return PluginSpecBroker
     */
    public function registerSpecs($specs)
    {
        if (!is_array($specs) && !$specs instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected array or Traversable object; received "%s"',
                (is_object($specs) ? get_class($specs) : gettype($specs))
            ));
        }
        foreach ($specs as $name => $spec) {
            $this->registerSpec($name, $spec);
        }
        return $this;
    }

    /**
     * Unregister a plugin specification
     * 
     * @param  string $name 
     * @return void
     */
    public function unregisterSpec($name)
    {
        $name = strtolower($name);
        if (array_key_exists($name, $this->specs)) {
            unset($this->specs[$name]);
        }
    }

    /**
     * Load and return a plugin instance
     *
     * If the plugin was previously loaded, returns that instance.
     *
     * If no options were passed, and we have no specification, load normally.
     *
     * If no options were passed, and we have a specification, use the 
     * specification to load an instance.
     *
     * Otherwise, simply try and load the plugin.
     * 
     * @param  string $plugin 
     * @param  array|null $options 
     * @return object
     * @throws Exception if plugin not found
     */
    public function load($plugin, array $options = null)
    {
        $pluginName = strtolower($plugin);
        if (isset($this->plugins[$pluginName])) {
            // If we've loaded it already, just return it
            return $this->plugins[$pluginName];
        }

        if ((null !== $options) || !isset($this->specs[$pluginName])) {
            // If we got options, or if there are no specs for this plugin,
            // simply proxy to the parent method
            return parent::load($plugin, $options);
        }

        if (isset($this->specs[$pluginName])) {
            // If we have a spec, pass the spec to the parent method
            return parent::load($plugin, $this->specs[$pluginName]);
        }

        // Return control to the parent method with original arguments
        return parent::load($plugin, $options);
    }

    /**
     * Retrieve a list of plugins and/or specs registered
     *
     * Differs from getPlugins() in that this will return true for both a 
     * plugin that has been loaded, as well as a plugin for which only a spec
     * is available.
     * 
     * @return array
     */
    public function getRegisteredPlugins()
    {
        $specNames   = array_keys($this->specs);
        $pluginNames = array_keys($this->plugins);
        return array_merge($specNames, $pluginNames);
    }

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
    public function hasPlugin($name)
    {
        $name = strtolower($name);
        return (isset($this->plugins[$name]) || array_key_exists($name, $this->specs));
    }
}
