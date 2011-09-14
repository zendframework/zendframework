<?php

namespace Zf2Module;

use Zend\Config\Config,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class ModuleManager
{
    /**
     * @var ModuleResolver
     */
    protected $loader;

    /**
     * @var array An array of Module classes of loaded modules
     */
    protected $modules = array();

    /**
     * @var EventCollection
     */
    protected $events;

    /**
     * getLoader 
     * 
     * @return ModuleResolver
     */
    public function getLoader()
    {
        if (!$this->loader instanceof ModuleResolver) {
            $this->setLoader(new ModuleLoader);
        }
        return $this->loader;
    }

    /**
     * setLoader 
     * 
     * @param ModuleResolver $loader 
     * @return ModuleManager
     */
    public function setLoader(ModuleResolver $loader)
    {
        $this->loader = $loader;
        return $this;
    }

    /**
     * loadModules 
     * 
     * @param array $modules 
     * @return ModuleManager
     */
    public function loadModules(array $modules)
    {
        foreach ($modules as $moduleName) {
            $this->loadModule($moduleName);
        }
        $this->events()->trigger('init.post', $this);
        return $this->modules;
    }

    /**
     * loadModule 
     * 
     * @param string $moduleName 
     * @return mixed Module's information class
     */
    public function loadModule($moduleName)
    {
        if (!isset($this->modules[$moduleName])) {
            $infoClass = $this->getLoader()->load($moduleName);
            $module = new $infoClass;
            if (is_callable(array($module, 'init'))) {
                $module->init($this->events());
            }
            $this->modules[$moduleName] = $module;
        }
        return $this->modules[$moduleName];
    }

    /**
     * getMergedConfig
     * Build a merged config object for all loaded modules
     * 
     * @return Zend\Config\Config
     */
    public function getMergedConfig()
    {
        $config = new Config(array(), true);
        foreach ($this->modules as $module) {
            if (is_callable(array($module, 'getConfig'))) {
                $config->merge($module->getConfig(defined('APPLICATION_ENV') ? APPLICATION_ENV : NULL));
            }
        }
        $config->setReadOnly();
        return $config;
    }

    /**
     * fromConfig 
     * Convenience method
     * 
     * @param Config $config 
     * @return ModuleManager
     */
    public static function fromConfig(Config $config)
    {
        if (!isset($config->modulesPath) || !isset($config->modules)) {

        }
        $moduleCollection = new static; 
        $moduleCollection->getLoader()->registerPaths($config->modulePaths->toArray());
        $moduleCollection->loadModules($config->modules->toArray());
        return $moduleCollection;
    }

    /**
     * Set the event manager instance used by this context
     * 
     * @param  EventCollection $events 
     * @return ModuleManager
     */
    public function setEventManager(EventCollection $events)
    {
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     * 
     * @return EventCollection
     */
    public function events()
    {
        if (!$this->events instanceof EventCollection) {
            $this->setEventManager(new EventManager(array(__CLASS__, get_class($this))));
        }
        return $this->events;
    }
}
