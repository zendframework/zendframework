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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Loader;

/**
 * Plugin broker base implementation
 *
 * @category   Zend
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PluginBroker implements Broker
{
    /**
     * @var string Default class loader to utilize with this broker
     */
    protected $defaultClassLoader = 'Zend\Loader\PluginClassLoader';

    /**
     * @var ShortNameLocater Plugin class loader used by this instance
     */
    protected $classLoader;

    /**
     * @var array Cache of loaded plugin instances
     */
    protected $plugins = array();

    /**
     * @var Callback Routine to use when validating plugins
     */
    protected $validator;

    /**
     * Load and return a plugin instance
     * 
     * @param  string $plugin 
     * @param  array $options Options to pass to the plugin constructor
     * @return Helper
     * @throws Exception if helper not found
     */
    public function load($plugin, array $options = null)
    {
        $pluginName = strtolower($plugin);
        if (isset($this->plugins[$pluginName])) {
            return $this->plugins[$pluginName];
        }

        $class = $this->getClassLoader()->load($plugin);
        if (empty($class)) {
            throw new Exception\RuntimeException('Unable to locate class associated with "' . $pluginName . '"');
        }

        if (empty($options)) {
            $instance = new $class();
        } else {
            $r = new \ReflectionClass($class);
            $instance = $r->newInstanceArgs($options);
        }

        $this->register($pluginName, $instance);
        return $instance;
    }

    /**
     * Get list of all loaded plugins
     * 
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Whether or not a given plugin has been loaded
     * 
     * @param  string $name 
     * @return bool
     */
    public function isLoaded($name)
    {
        return isset($this->plugins[$name]);
    }

    /**
     * Register a plugin object by name
     * 
     * @param  string $name 
     * @param  mixed $plugin 
     * @return PluginBroker
     */
    public function register($name, $plugin)
    {
        if (!$this->validatePlugin($plugin)) {
            throw new Exception\RuntimeException();
        }

        $this->plugins[$name] = $plugin;
        return $this;
    }

    /**
     * Unregister a named plugin
     *
     * Removes the plugin instance from the registry, if found.
     * 
     * @param  string $name 
     * @return bool
     */
    public function unregister($name)
    {
        if (isset($this->plugins[$name])) {
            unset($this->plugins[$name]);
            return true;
        }
        return false;
    }

    /**
     * Set class loader to use when resolving plugin names to class names
     * 
     * @param  ShortNameLocater $loader 
     * @return PluginBroker
     */
    public function setClassLoader(ShortNameLocater $loader)
    {
        $this->classLoader = $loader;
        return $this;
    }

    /**
     * Retrieve the class loader
     *
     * Lazy-loads an instance of PluginClassLocater if no loader is registered.
     * 
     * @return ShortNameLocater
     */
    public function getClassLoader()
    {
        if (null === $this->classLoader) {
            $loaderClass = $this->defaultClassLoader;
            $this->setClassLoader(new $loaderClass());
        }
        return $this->classLoader;
    }

    /**
     * Set plugin validator callback
     * 
     * @param  callback $callback 
     * @return PluginBroker
     */
    public function setValidator($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidArgumentException(sprintf('Validator must be a valid PHP callback; %s provided', gettype($callback)));
        }
        $this->validator = $callback;
        return $this;
    }

    /**
     * Retrieve plugin validator callback
     * 
     * @return null|callback
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Determine whether we have a valid plugin
     *
     * Override this method to implement custom validation logic. Typically, 
     * throw a custom exception for invalid plugins.
     * 
     * @param  mixed $plugin 
     * @return bool
     */
    protected function validatePlugin($plugin)
    {
        if (null !== ($validator = $this->getValidator())) {
            return call_user_func($validator, $plugin);
        }
        return true;
    }
}
