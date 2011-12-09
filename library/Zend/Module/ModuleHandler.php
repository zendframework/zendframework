<?php

namespace Zend\Module;

use Zend\EventManager\EventCollection;

interface ModuleHandler
{
    /**
     * Load the provided modules.
     *
     * @return ManagerHandler
     */
    public function loadModules();

    /**
     * Load a specific module by name.
     *
     * @param string $moduleName
     * @return mixed Module's Module class
     */
    public function loadModule($moduleName);

    /**
     * Get an array of the loaded modules.
     *
     * @param bool $loadModules If true, load modules if they're not already
     * @return array An array of Module objects, keyed by module name
     */
    public function getLoadedModules($loadModules);

    /**
     * Get the array of module names that this manager should load.
     *
     * @return array
     */
    public function getModules();

    /**
     * Set an array or Traversable of module names that this module manager should load.
     *
     * @param mixed $modules array or Traversable of module names
     * @return ModuleHandler
     */
    public function setModules($modules);

    /**
     * Set the event manager instance used by this module manager.
     *
     * @param  EventCollection $events
     * @return ManagerHandler
     */
    public function setEventManager(EventCollection $events);

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventCollection
     */
    public function events();
}
