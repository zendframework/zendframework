<?php

namespace Zend\Module;

use Traversable,
    Zend\Module\Listener\ListenerOptions,
    Zend\Module\Listener\AutoloaderListener,
    Zend\Module\Listener\ConfigListener,
    Zend\Module\Listener\InitTrigger,
    Zend\Module\Listener\ConfigMerger,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class Manager implements ModuleHandler
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
     * modules 
     * 
     * @var array|Traversable
     */
    protected $modules = array();

    /**
     * True if modules have already been loaded
     *
     * @var bool
     */
    protected $modulesAreLoaded = false;

    /**
     * Config listener 
     * 
     * @var mixed
     */
    protected $configListener;

    /**
     * If true, will not register the default config/init listeners 
     * 
     * @var bool
     */
    protected $disableLoadDefaultListeners = false;

    /**
     * Options for the default listeners 
     * 
     * @var ListenerOptions
     */
    protected $defaultListenerOptions;

    /**
     * __construct 
     * 
     * @param array|Traversable $modules 
     * @return void
     */
    public function __construct($modules)
    {
        $this->setModules($modules);
    }

    /**
     * Load the provided modules.
     * 
     * @return ManagerHandler
     */
    public function loadModules()
    {
        if (true === $this->modulesAreLoaded) {
            return $this;
        }
        foreach ($this->getModules() as $moduleName) {
            $this->loadModule($moduleName);
        }
        if ($configListener = $this->getConfigListener()) {
            $configListener->mergeConfigGlobPaths();
        }
        $this->events()->trigger('init.post', $this);
        $this->modulesAreLoaded = true;
        return $this;
    }

    /**
     * Load a specific module by name.
     * 
     * @param string $moduleName 
     * @return mixed Module's Module class
     */
    public function loadModule($moduleName)
    {
        if (isset($this->loadedModules[$moduleName])) {
            return $this->loadedModules[$moduleName];
        }

        $class = $moduleName . '\Module';
        
        if (!class_exists($class)) {
            throw new Exception\RuntimeException(sprintf(
                'Module (%s) could not be initialized because Module.php could not be found.',
                $moduleName
            ));
        }
        
        $module = new $class;
        $event  = new ModuleEvent();
        $event->setModule($module);
        $this->events()->trigger(__FUNCTION__, $this, $event);
        $this->loadedModules[$moduleName] = $module;
        return $module;
    }

    /**
     * Get an array of the loaded modules.
     *
     * @param bool $loadModules If true, load modules if they're not already
     * @return array An array of Module objects, keyed by module name
     */
    public function getLoadedModules($loadModules = false)
    {
        if (true === $loadModules) {
            $this->loadModules();
        }
        return $this->loadedModules;
    }

    /**
     * Get the array of module names that this manager should load.
     *
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }
 
    /**
     * Set an array or Traversable of module names that this module manager should load. 
     *
     * @param mixed $modules array or Traversable of module names
     * @return ModuleHandler
     */
    public function setModules($modules)
    {
        if (is_array($modules) || $modules instanceof Traversable) {
            $this->modules = $modules;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter to %s\'s %s method must be an array or implement the \\Traversable interface',
                __CLASS__, __METHOD__
            ));
        }
        return $this;
    }
    
    /**
     * Get the listener that's in charge of merging module configs.
     *
     * @param bool $autoInstantiate 
     * @return ConfigMerger
     */
    public function getConfigListener($autoInstantiate = true)
    {
        if (true === $autoInstantiate) {
            $this->events();
        }
        return $this->configListener;
    }
 
    /**
     * Set the listener that's in charge of merging module configs.
     *
     * @param ConfigMerger $configListener
     * @return ModuleHandler
     */
    public function setConfigListener(ConfigMerger $configListener)
    {
        $this->configListener = $configListener;
        return $this;
    }

    /**
     * A convenience method that proxies through to:
     *
     * $this->getConfigListener()->getMergedConfig($returnConfigAsObject);
     * 
     * @param bool $returnConfigAsObject 
     * @return mixed
     */
    public function getMergedConfig($returnConfigAsObject = true)
    {
        return $this->getConfigListener()->getMergedConfig($returnConfigAsObject);
    }

    /**
     * Set the event manager instance used by this module manager.
     * 
     * @param  EventCollection $events 
     * @return ManagerHandler
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
            $this->setDefaultListeners();
        }
        return $this->events;
    }

    /**
     * Set if the default listeners should be registered or not
     * 
     * @param bool $flag 
     * @return Manager
     */
    public function setDisableLoadDefaultListeners($flag)
    {
        $this->disableLoadDefaultListeners = (bool) $flag;
        return $this;
    }

    /**
     * Return if the default listeners are disabled or not
     * 
     * @return bool
     */
    public function loadDefaultListenersIsDisabled()
    {
        return $this->disableLoadDefaultListeners;
    }

    /**
     * Internal method for attaching the default listeners
     * 
     * @return Manager
     */
    protected function setDefaultListeners()
    {
        if ($this->loadDefaultListenersIsDisabled()) {
            return $this;
        }
        $options = $this->getDefaultListenerOptions();
        if (null === $this->getConfigListener(false)) {
            $this->setConfigListener(new ConfigListener($options));
        }
        $this->events()->attach('loadModule', new InitTrigger($options), 1000);
        $this->events()->attach('loadModule', $this->getConfigListener(), 1000);
        $this->events()->attach('loadModule', new AutoloaderListener($options), 2000); // Should be called before init
        return $this;
    }
 
    /**
     * Get the options for the default module listeners.
     *
     * @return ListenerOptions
     */
    public function getDefaultListenerOptions()
    {
        if (null === $this->defaultListenerOptions) {
            $this->defaultListenerOptions =  new ListenerOptions;
        }
        return $this->defaultListenerOptions;
    }
 
    /**
     * Set the options for the default module listeners.
     *
     * @param ListenerOptions $defaultListenerOptions
     * @return Manager
     */
    public function setDefaultListenerOptions(ListenerOptions $defaultListenerOptions)
    {
        $this->defaultListenerOptions = $defaultListenerOptions;
        return $this;
    }
 
}
