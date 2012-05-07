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

use Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventsCapableInterface;

/**
 * Module handler interface
 *
 * @category Zend
 * @package  Zend_Module
 */
interface ModuleHandler extends EventManagerAwareInterface, EventsCapableInterface
{
    /**
     * Load the provided modules.
     *
     * @return ModuleHandler
     */
    public function loadModules();

    /**
     * Load a specific module by name.
     *
     * @param  string $moduleName
     * @return mixed Module's Module class
     */
    public function loadModule($moduleName);

    /**
     * Get an array of the loaded modules.
     *
     * @param  bool $loadModules If true, load modules if they're not already
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
     * @param  mixed $modules array or Traversable of module names
     * @return ModuleHandler
     */
    public function setModules($modules);
}
