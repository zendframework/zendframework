<?php

namespace Zf2Module;

use Traversable,
    Zend\Config\Config,
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
    protected $loadedModules = array();

    /**
     * @var EventCollection
     */
    protected $events;

    /**
     * @var ModuleManagerOptions
     */
    protected $options;

    /**
     * __construct 
     * 
     * @param ModuleLoader $loader 
     * @param array|Traversable $modules 
     * @param ModuleManagerOptions $options 
     * @return void
     */
    public function __construct(ModuleLoader $loader, $modules, ModuleManagerOptions $options = null)
    {
        if ($options === null) {
            $this->setOptions(new ModuleManagerOptions);
        }
        $this->setLoader($loader);
        $this->loadModules($modules);
    }

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
     * @param array|Traversable $modules 
     * @return ModuleManager
     */
    public function loadModules($modules)
    {
        if (is_array($modules) || $modules instanceof Traversable) {
            foreach ($modules as $moduleName) {
                $this->loadModule($moduleName);
            }
        } else {
            throw new \InvalidArgumentException(
                'Parameter to \\Zf2Module\\ModuleManager\'s '
                . 'loadModules method must be an array or '
                . 'implement the \\Traversable interface'
            );
        }
        $this->events()->trigger('init.post', $this);
        return $this;
    }

    /**
     * loadModule 
     * 
     * @param string $moduleName 
     * @return mixed Module's information class
     */
    public function loadModule($moduleName)
    {
        if (!isset($this->loadedModules[$moduleName])) {
            $infoClass = $this->getLoader()->load($moduleName);
            $module = new $infoClass;
            if (is_callable(array($module, 'init'))) {
                $module->init($this->events());
            }
            $this->loadedModules[$moduleName] = $module;
        }
        return $this->loadedModules[$moduleName];
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
        foreach ($this->loadedModules as $module) {
            if (is_callable(array($module, 'getConfig'))) {
                $config->merge($module->getConfig(defined('APPLICATION_ENV') ? APPLICATION_ENV : NULL));
            }
        }
        $config->setReadOnly();
        return $config;
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

 
    /**
     * Get options.
     *
     * @return ModuleManagerOptions
     */
    public function getOptions()
    {
        return $this->options;
    }
 
    /**
     * Set options 
     * 
     * @param ModuleManagerOptions $options 
     * @return ModuleManager
     */
    public function setOptions(ModuleManagerOptions $options)
    {
        $this->options = $options;
        return $this;
    }
 
}
