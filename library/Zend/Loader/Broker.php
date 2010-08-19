<?php

namespace Zend\Loader;

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
     * @param  ShortNameLocater $loader 
     * @return void
     */
    public function setClassLoader(ShortNameLocater $loader);

    /**
     * Retrieve the plugin class loader
     * 
     * @return ShortNameLocater
     */
    public function getClassLoader();
}
