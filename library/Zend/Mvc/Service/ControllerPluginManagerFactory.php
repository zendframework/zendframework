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

use Zend\Mvc\Controller\PluginManager as ControllerPluginManager;
use Zend\Mvc\Exception;
use Zend\ServiceManager\ConfigurationInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ControllerPluginManagerFactory implements FactoryInterface
{
    /**
     * Create and return the MVC controller plugin manager
     * 
     * @param  ServiceLocatorInterface $serviceLocator 
     * @return ControllerPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugins = new ControllerPluginManager();

        // Configure additional plugins
        $config = $serviceLocator->get('Configuration');
        $map    = (isset($config['controller']) && isset($config['controller']['map'])) 
                ? $config['controller']['map']
                : array();
        foreach ($map as $key => $service) {
            if ((!is_string($key) || is_numeric($key))
                && class_exists($service)
            ) {
                $config = new $service;
                if (!$config instanceof ConfigurationInterface) {
                    throw new Exception\RuntimeException(sprintf(
                        'Invalid controller plugin configuration map provided; received "%s", expected class implementing %s',
                        $service, 
                        'Zend\ServiceManager\ConfigurationInterface'
                    ));
                }
                $config->configureServiceManager($plugins);
                continue;
            }
            $plugins->setInvokableClass($key, $service);
        }

        if ($serviceLocator instanceof ServiceManager) {
            $plugins->addPeeringServiceManager($serviceLocator);
        }

        return $plugins;
    }
}
