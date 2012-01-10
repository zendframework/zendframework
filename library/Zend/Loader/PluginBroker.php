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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Loader;

use Zend\Di\Locator;

/**
 * Plugin broker base implementation
 *
 * @category   Zend
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PluginBroker implements Broker, LocatorAware
{
    /**
     * @var string Default class loader to utilize with this broker
     */
    protected $defaultClassLoader = 'Zend\Loader\PluginClassLoader';

    /**
     * @var ShortNameLocator Plugin class loader used by this instance
     */
    protected $classLoader;
    
    /**
     * @var boolean Whether plugins should be registered on load
     */
    protected $registerPluginsOnLoad = true;

    /**
     * @var array Cache of loaded plugin instances
     */
    protected $plugins = array();

    /**
     * @var Callback Routine to use when validating plugins
     */
    protected $validator;

    /**
     * @var Zend\Di\Locator
     */
    protected $locator;

    /**
     * Constructor
     *
     * Allow configuration via options; see {@link setOptions()} for details.
     * 
     * @param  null|array|Traversable $options 
     * @return void
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure plugin broker
     * 
     * @param  array|Traversable $options 
     * @return PluginBroker
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array or Traversable; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        // Cache plugins until after a validator has been registered
        $plugins = array();

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'class_loader':
                    if (is_string($value)) {
                        if (!class_exists($value)) {
                            throw new Exception\RuntimeException(sprintf(
                                'Unknown class "%s" provided as class loader option',
                                $value
                            ));
                        }
                        $value = new $value;
                    }
                    if ($value instanceof ShortNameLocator) {
                        $this->setClassLoader($value);
                        break;
                    } 

                    if (!is_array($value) && !$value instanceof \Traversable) {
                        throw new Exception\RuntimeException(sprintf(
                            'Option passed for class loader (%s) is of an unknown type',
                            (is_object($value) ? get_class($value) : gettype($value))
                        ));
                    }

                    $class   = false;
                    $options = null;
                    foreach ($value as $k => $v) {
                        switch (strtolower($k)) {
                            case 'class':
                                $class = $v;
                                break;
                            case 'options':
                                $options = $v;
                                break;
                            default:
                                break;
                        }
                    }
                    if ($class) {
                        $loader = new $class($options);
                        $this->setClassLoader($loader);
                    }
                    break;
                case 'plugins':
                    if (!is_array($value) && !$value instanceof \Traversable) {
                        throw new Exception\RuntimeException(sprintf(
                            'Plugins option must be an array or Traversable; received "%s"',
                            (is_object($value) ? get_class($value) : gettype($value))
                        ));
                    }

                    // Aggregate plugins; register only after a validator has 
                    // been registered
                    $plugins = $value;
                    break;
                case 'register_plugins_on_load':
                    $this->setRegisterPluginsOnLoad($value);
                    break;
                case 'validator':
                    $this->setValidator($value);
                    break;
                default:
                    // ignore unknown options
                    break;
            }
        }

        // Register any plugins discovered
        foreach ($plugins as $name => $plugin) {
            $this->register($name, $plugin);
        }

        return $this;
    }

    /**
     * Load and return a plugin instance
     * 
     * @param  string $plugin 
     * @param  array $options Options to pass to the plugin constructor
     * @return object
     * @throws Exception if plugin not found
     */
    public function load($plugin, array $options = null)
    {
        $pluginName = strtolower($plugin);
        if (isset($this->plugins[$pluginName])) {
            return $this->plugins[$pluginName];
        }

        if (class_exists($plugin)) {
            // Allow loading fully-qualified class names via the broker
            $class = $plugin;
        } else {
            // Unqualified class names are then passed to the class loader
            $class = $this->getClassLoader()->load($plugin);
            if (empty($class)) {
                throw new Exception\RuntimeException('Unable to locate class associated with "' . $pluginName . '"');
            }
        }

        if ($this->getLocator()) {
            if (empty($options)) {
                $instance = $this->getLocator()->get($class);
            } elseif ($this->isAssocArray($options)) {
                // This might be inconsistent with what $options should be?
                $instance = $this->getLocator()->get($class, $options);
            } else {
                // @TODO: Clean this up, somehow?
                $instance = $this->getLocator()->get($class);
            }
        } else {
            if (empty($options)) {
                $instance = new $class();
            } elseif ($this->isAssocArray($options)) {
                $instance = new $class($options);
            } else {
                $r = new \ReflectionClass($class);
                $instance = $r->newInstanceArgs($options);
            }
        }

        if ($this->getRegisterPluginsOnLoad()) {
            $this->register($pluginName, $instance);
        }
        
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

        $name = strtolower($name);

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
        $name = strtolower($name);
        if (isset($this->plugins[$name])) {
            unset($this->plugins[$name]);
            return true;
        }
        return false;
    }

    /**
     * Set class loader to use when resolving plugin names to class names
     * 
     * @param  ShortNameLocator $loader 
     * @return PluginBroker
     */
    public function setClassLoader(ShortNameLocator $loader)
    {
        $this->classLoader = $loader;
        return $this;
    }

    /**
     * Retrieve the class loader
     *
     * Lazy-loads an instance of PluginClassLocator if no loader is registered.
     * 
     * @return ShortNameLocator
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
     * Set if plugins should be registered on load.
     * 
     * @param  boolean $flag
     * @return PluginBroker
     */
    public function setRegisterPluginsOnLoad($flag)
    {
        $this->registerPluginsOnLoad = (bool) $flag;
        return $this;
    }

    /**
     * Retrieve if plugins are registered on load.
     * 
     * @return boolean
     */
    public function getRegisterPluginsOnLoad()
    {
        return $this->registerPluginsOnLoad;
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

    /**
     * Is a value an associative array?
     * 
     * @param  mixed $value 
     * @return bool
     */
    protected function isAssocArray($value)
    {
        if (!is_array($value)) {
            return false;
        }
        if (array_keys($value) === range(0, count($value) - 1)) {
            return false;
        }
        return true;
    }
 
    /**
     * Get locator. 
     * 
     * @return Zend\Di\Locator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Set locator.
     *
     * @param Zend\Di\Locator $locator
     */
    public function setLocator(Locator $locator)
    {
        $this->locator = $locator;
        return $this;
    }
}
