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
     * @var boolean
     */
    protected $modulesLoaded = false;

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
        }
        return $this->events;
    }
}
