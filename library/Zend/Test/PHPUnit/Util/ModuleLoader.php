<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Test\PHPUnit\Util;

use PHPUnit_Framework_TestCase;
use Zend\Mvc\Service;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Exception;

abstract class ModuleLoader extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Load module
     * @param string $moduleName
     */
    public function loadModule($moduleName)
    {
        if (!is_array($moduleName)) {
            $moduleName = array($moduleName);
        }
        $this->loadModules($moduleName);
    }

    /**
     * Load modules
     * @param array $modules
     */
    public function loadModules(array $modules)
    {
        $modulesPath = array();
        $modulesList = array();

        foreach ($modules as $key => $module) {
            if (is_numeric($key)) {
                $modulesList[] = $module;
                continue;
            }
            $modulesList[] = $key;
            $modulesPath[$key] = $module;
        }
        $config = array(
            'module_listener_options' => array(
                'module_paths' => $modulesPath,
            ),
            'modules' => $modulesList,
        );
        $this->loadModulesFromConfig($config);
    }

    /**
     * Load modules from configuration
     * @param string $configuration
     */
    public function loadModulesFromConfig($configuration)
    {
        $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : array();
        $this->serviceManager = new ServiceManager(new Service\ServiceManagerConfig($smConfig));
        $this->serviceManager->setService('ApplicationConfig', $configuration);
        $this->serviceManager->get('ModuleManager')->loadModules();
    }

    /**
     * Get an instance of a module class by the module name
     *
     * @param  string $moduleName
     * @return mixed
     */
    public function getModule($moduleName)
    {
        return $this->serviceManager->get('ModuleManager')->getModule($moduleName);
    }

    /**
     * Get the array of module names that this manager should load.
     *
     * @return array
     */
    public function getModules()
    {
        return $this->serviceManager->get('ModuleManager')->getModules();
    }

    /**
     * Get the service manager
     * @var ServiceManager
     */
    public function getServiceManager()
    {
        if (null === $this->serviceManager) {
            throw new Exception\LogicException('You must load modules before to have access to the service manager');
        }

        return $this->serviceManager;
    }
}
