<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Module
 */
namespace Zend\Module;

use Traversable,
    Zend\EventManager\EventManagerInterface,
    Zend\EventManager\EventManager;

/**
 * Module manager
 * 
 * @category Zend
 * @package  Zend_Module
 */
class Manager implements ModuleHandler
{
    /**
     * @var array An array of Module classes of loaded modules
     */
    protected $loadedModules = array();

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var ModuleEvent
     */
    protected $event;

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
     * Constructor
     *
     * @param  array|Traversable $modules
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function __construct($modules, EventManagerInterface $eventManager = null)
    {
        $this->setModules($modules);
        if ($eventManager instanceof EventManagerInterface) {
            $this->setEventManager($eventManager);
        }
    }

    /**
     * Load the provided modules.
     *
     * @triggers loadModules.pre
     * @triggers loadModules.post
     * @return   Manager
     */
    public function loadModules()
    {
        if (true === $this->modulesAreLoaded) {
            return $this;
        }

        $this->events()->trigger(__FUNCTION__ . '.pre', $this, $this->getEvent());

        foreach ($this->getModules() as $moduleName) {
            $this->loadModule($moduleName);
        }

        $this->events()->trigger(__FUNCTION__ . '.post', $this, $this->getEvent());

        $this->modulesAreLoaded = true;
        return $this;
    }

    /**
     * Load a specific module by name.
     *
     * @param    string $moduleName
     * @triggers loadModule.resolve
     * @triggers loadModule
     * @return   mixed Module's Module class
     */
    public function loadModule($moduleName)
    {
        if (isset($this->loadedModules[$moduleName])) {
            return $this->loadedModules[$moduleName];
        }

        $event = $this->getEvent();
        $event->setModuleName($moduleName);

        $result = $this->events()->trigger(__FUNCTION__ . '.resolve', $this, $event, function ($r) {
            return (is_object($r));
        });

        $module = $result->last();

        if (!is_object($module)) {
            throw new Exception\RuntimeException(sprintf(
                'Module (%s) could not be initialized.',
                $moduleName
            ));
        }
        $event->setModule($module);

        $this->events()->trigger(__FUNCTION__, $this, $event);
        $this->loadedModules[$moduleName] = $module;
        return $module;
    }

    /**
     * Get an array of the loaded modules.
     *
     * @param  bool $loadModules If true, load modules if they're not already
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
     * Get an instance of a module class by the module name 
     * 
     * @param  string $moduleName 
     * @return mixed
     */
    public function getModule($moduleName)
    {
        if (!isset($this->loadedModules[$moduleName])) {
            return null;
        }
        return $this->loadedModules[$moduleName];
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
     * @param  mixed $modules array or Traversable of module names
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
     * Get the module event
     *
     * @return ModuleEvent
     */
    public function getEvent()
    {
        if (!$this->event instanceof ModuleEvent) {
            $this->setEvent(new ModuleEvent);
        }
        return $this->event;
    }

    /**
     * Set the module event
     *
     * @param  ModuleEvent $event
     * @return Manager
     */
    public function setEvent(ModuleEvent $event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Set the event manager instance used by this module manager.
     *
     * @param  EventManagerInterface $events
     * @return Manager
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(__CLASS__, get_class($this)));
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function events()
    {
        if (!$this->events instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
