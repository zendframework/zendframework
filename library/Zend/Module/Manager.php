<?php

namespace Zend\Module;

use Traversable,
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
    protected $modulesLoaded = false;

    /**
     * If true, will not register the default config/init listeners 
     * 
     * @var bool
     */
    protected $disableLoadDefaultListeners = false;

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
        $this->events()->trigger('init.post', $this);
        $this->modulesLoaded = true;
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
            
            if (!class_exists($class)) {
                throw new Exception\RuntimeException(sprintf(
                    'Module (%s) could not be initialized because Module.php could not be found.',
                    $moduleName
                ));
            }
            
            $module = new $class;
            $this->events()->trigger(__FUNCTION__, $this, array('module' => $module));
            $this->loadedModules[$moduleName] = $module;
        }
        return $this->loadedModules[$moduleName];
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
            $this->setDefaultListeners();
        }
        return $this->events;
    }

    /**
     * Returns the merged config after modules have been loaded. This requires a 
     * Listener\ConfigListener to be registered.
     *
     * Before modules are loaded, or if no Listener\ConfigListeners, this will return false.
     * 
     * @param bool $returnConfigAsObject Set to false to return as plain array
     * @return mixed
     */
    public function getMergedConfig($returnConfigAsObject = true)
    {
        $listeners = $this->events()->getListeners('loadModule');
        foreach ($listeners as $listener) {
            $listener = $listener->getCallback();
            if ($listener instanceof Listener\ConfigListener) {
                return $listener->getMergedConfig($returnConfigAsObject);
            }
        }
        return false;
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
        $init     = new Listener\InitTrigger;
        $config   = new Listener\ConfigListener;
        $autoload = new Listener\AutoloaderTrigger;
        $this->events()->attach('loadModule', $init, 1000);
        $this->events()->attach('loadModule', $config, 1000);
        $this->events()->attach('loadModule', $autoload, 1000);
        return $this;
    }
}
