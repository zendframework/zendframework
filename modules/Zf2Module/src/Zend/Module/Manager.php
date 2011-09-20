<?php

namespace Zend\Module;

use Traversable,
    Zend\Config\Config,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class Manager
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
     * @var ManagerOptions
     */
    protected $options;

    /**
     * __construct 
     * 
     * @param array|Traversable $modules 
     * @param ManagerOptions $options 
     * @return void
     */
    public function __construct($modules, ManagerOptions $options = null)
    {
        if ($options === null) {
            $this->setOptions(new ManagerOptions);
        } else {
            $this->setOptions($options);
        }
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
     * @return Manager
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
     * @return Manager
     */
    public function loadModules($modules)
    {
        if (is_array($modules) || $modules instanceof Traversable) {
            foreach ($modules as $moduleName) {
                $this->loadModule($moduleName);
            }
        } else {
            throw new \InvalidArgumentException(
                'Parameter to \\Zf2Module\\Manager\'s '
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
     * @return mixed Module's Module class
     */
    public function loadModule($moduleName)
    {
        if (!isset($this->loadedModules[$moduleName])) {
            $class = $moduleName . '\Module';
            $module = new $class;
            if (is_callable(array($module, 'init'))) {
                $module->init($this->events());
            }
            $this->loadedModules[$moduleName] = $module;
        }
        return $this->loadedModules[$moduleName];
    }


    /**
     * Set the event manager instance used by this context
     * 
     * @param  EventCollection $events 
     * @return Manager
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
     * @return ManagerOptions
     */
    public function getOptions()
    {
        return $this->options;
    }
 
    /**
     * Set options 
     * 
     * @param ManagerOptions $options 
     * @return Manager
     */
    public function setOptions(ManagerOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * getMergedConfig
     * Build a merged config object for all loaded modules
     * 
     * @return Zend\Config\Config
     */
    public function getMergedConfig()
    {
        if (($config = $this->getCachedConfig()) !== false) {
            return $config;
        }
        $config = new Config(array(), true);
        foreach ($this->loadedModules as $module) {
            if (is_callable(array($module, 'getConfig'))) {
                $config->merge($module->getConfig($this->getOptions()->getApplicationEnv()));
            }
        }
        $config->setReadOnly();
        if ($this->getOptions()->getCacheConfig()) {
            $this->saveConfigCache($config);
        }
        return $config;
    }

    protected function hasCachedConfig()
    {
        if($this->getOptions()->getCacheConfig()) {
            if (file_exists($this->getOptions()->getCacheFilePath())) {
                return true;
            }
        }
        return false;
    }

    protected function getCachedConfig()
    {
        if ($this->hasCachedConfig()) {
            return new Config(include $this->getOptions()->getCacheFilePath());
        }
        return false; 
    }

    protected function saveConfigCache($config)
    {
        $content = "<?php\nreturn " . var_export($config->toArray(), 1) . ';';
        file_put_contents($this->getOptions()->getCacheFilePath(), $content);
        return $this;
    }
}
