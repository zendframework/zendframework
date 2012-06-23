<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ConfigurationFactory implements FactoryInterface
{
    /**
     * Create the application configuration service
     *
     * Retrieves the Module Manager from the service locator, and executes 
     * {@link Zend\ModuleManager\ModuleManager::loadModules()}.
     *
     * It then retrieves the config listener from the module manager, and from
     * that the merged configuration.
     * 
     * @param  ServiceLocatorInterface $serviceLocator 
     * @return array|\Traversable
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mm           = $serviceLocator->get('ModuleManager');
        $mm->loadModules();
        $moduleParams = $mm->getEvent()->getParams();
        $config       = $moduleParams['configListener']->getMergedConfig(false);
        return $config;
    }
}
