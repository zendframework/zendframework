<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Test\Util;

use Zend\Mvc\Service;
use Zend\ServiceManager\ServiceManager;

class ModuleLoader
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Load list of modules or application configuration
     * @param array $modules
     */
    public function __construct(array $modules)
    {
        if (!isset($modules['modules'])) {
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
            $configuration = array(
                'module_listener_options' => array(
                    'module_paths' => $modulesPath,
                ),
                'modules' => $modulesList,
            );
        } else {
            $configuration = $modules;
        }

        $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : array();
        $this->serviceManager = new ServiceManager(new Service\ServiceManagerConfig($smConfig));
        $this->serviceManager->setService('ApplicationConfig', $configuration);
        $this->serviceManager->get('ModuleManager')->loadModules();
    }

    /**
     * Get the application
     * @return Zend\Mvc\Application
     */
    public function getApplication()
    {
        return $this->serviceManager->get('ModuleManager');
    }

    /**
     * Get the module manager
     * @return Zend\ModuleManager\ModuleManager
     */
    public function getModuleManager()
    {
        return $this->serviceManager->get('ModuleManager');
    }

    /**
     * Get module
     * @return mixed
     */
    public function getModule($moduleName)
    {
        return $this->serviceManager->get('ModuleManager')->getModule($moduleName);
    }

    /**
     * Get the service manager
     * @var ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}
