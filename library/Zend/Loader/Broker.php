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
