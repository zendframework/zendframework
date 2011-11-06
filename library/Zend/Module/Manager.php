<?php

namespace Zend\Module;

use Traversable,
    Zend\Config\Config,
    Zend\Config\Writer\ArrayWriter,
    Zend\Stdlib\IteratorToArray,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class Manager
{
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
     * @var array
     */
    protected $mergedConfig = array();

    /**
     * @var Config
     */
    protected $mergedConfigObject;

    /**
     * @var bool
     */
    protected $skipConfig = false;

    /**
     * modules 
     * 
     * @var array|Traversable
     */
    protected $modules = array();

    /**
     * True if modules have already been loaded
     *
     * @var boolean
     */
    protected $modulesLoaded = false;

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
            $options = new ManagerOptions;
        }
        $this->setOptions($options);
        if ($this->hasCachedConfig()) {
            $this->skipConfig = true;
            $this->setMergedConfig($this->getCachedConfig());
        }
        $this->setModules($modules);
    }

    /**
     * loadModules 
     * 
     * @return Manager
     */
    public function loadModules()
    {
        if ($this->modulesLoaded === true) {
            return $this;
        }
        foreach ($this->getModules() as $moduleName) {
            $this->loadModule($moduleName);
        }
        if ($this->getOptions()->getEnableDependencyCheck()) {
            $this->resolveDependencies();
        }
        $this->updateCache();
        $this->events()->trigger('init.post', $this);
        $this->modulesLoaded = true;
        return $this;
    }

    /**
     * Returns boolean representing if modules have been loaded yet 
     * 
     * @return Manager
     */
    public function modulesLoaded()
    {
        return $this->modulesLoaded;
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
            
            if (!class_exists($class)) {
                throw new Exception\RuntimeException(sprintf(
                    'Module (%s) could not be initialized because Module.php could not be found.',
                    $moduleName
                ));
            }
            
            $module = new $class;
            $this->runModuleInit($module);
            $this->mergeModuleConfig($module);
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
     * Get loadedModules.
     *
     * @param bool $loadModules 
     * @return array
     */
    public function getLoadedModules($loadModules = false)
    {
        if ($loadModules === true) {
            $this->loadModules();
        }
        return $this->loadedModules;
    }

    /**
     * getMergedConfig
     * 
     * @param array $returnConfigAsObject 
     * @return mixed
     */
    public function getMergedConfig($returnConfigAsObject = true)
    {
        if ($returnConfigAsObject === true) {
            if ($this->mergedConfigObject === null) {
                $this->mergedConfigObject = new Config($this->mergedConfig);
            }
            return $this->mergedConfigObject;
        } else {
            return $this->mergedConfig;
        }
    }

    /**
     * setMergedConfig 
     * 
     * @param array $config 
     * @return Manager
     */
    protected function setMergedConfig($config)
    {
        $this->mergedConfig = $config;
        return $this;
    }

    /**
     * mergeModuleConfig 
     * 
     * @param mixed $module 
     * @return Manager
     */
    protected function mergeModuleConfig($module)
    {
        if ((false === $this->skipConfig)
            && (is_callable(array($module, 'getConfig')))
        ) {
            $config = $module->getConfig($this->getOptions()->getApplicationEnv());
            if ($config instanceof Traversable) {
                $config = IteratorToArray::convert($config);
            }
            if (!is_array($config)) {
                throw new \InvalidArgumentException(
                    sprintf('getConfig() method of %s must be an array, '
                    . 'implement the \Traversable interface, or be an '
                    . 'instance of Zend\Config\Config', get_class($module))
                );
            }
            $this->mergedConfig = array_replace_recursive($this->mergedConfig, $config);
        }
        return $this;
    }

    protected function runModuleInit($module)
    {
        if (is_callable(array($module, 'init'))) {
            $module->init($this);
        }
        return $this;
    }

    protected function hasCachedConfig()
    {
        if (($this->getOptions()->getEnableConfigCache())
            && (file_exists($this->getOptions()->getCacheFilePath()))
        ) {
            return true;
        }
        return false;
    }

    protected function getCachedConfig()
    {
        return include $this->getOptions()->getCacheFilePath();
    }

    protected function updateCache()
    {
        if (($this->getOptions()->getEnableConfigCache())
            && (false === $this->skipConfig)
        ) {
            $this->saveConfigCache($this->getMergedConfig(false));
        }
        return $this;
    }

    protected function saveConfigCache($config)
    {
        $content = "<?php\nreturn " . var_export($config, 1) . ';';
        file_put_contents($this->getOptions()->getCacheFilePath(), $content);
        return $this;
    }
 
    /**
     * Get modules.
     *
     * @return modules
     */
    public function getModules()
    {
        return $this->modules;
    }
 
    /**
     * Set modules.
     *
     * @param $modules the value to be set
     */
    public function setModules($modules)
    {
        if (is_array($modules) || $modules instanceof Traversable) {
            $this->modules = $modules;
        } else {
            throw new \InvalidArgumentException(
                'Parameter to ' . __CLASS__ . '\'s '
                . __METHOD__ . ' method must be an array or '
                . 'implement the \\Traversable interface'
            );
        }
        return $this;
    }
}
