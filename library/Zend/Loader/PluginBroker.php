<?php

namespace Zend\Loader;

class PluginBroker implements Broker
{
    protected $defaultClassLoader = 'Zend\Loader\PluginClassLoader';
    protected $classLoader;
    protected $plugins = array();

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
            throw new InvalidPluginException('Unable to locate class associated with "' . $pluginName . '"');
        }

        if (empty($options)) {
            $instance = new $class();
        } else {
            $r = new \ReflectionClass($class);
            $instance = $r->newInstanceArgs($options);
        }

        if (!$this->validatePlugin($instance)) {
            throw new InvalidPluginException();
        }

        $this->plugins[$pluginName] = $instance;
        return $instance;
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
        return true;
    }
}
