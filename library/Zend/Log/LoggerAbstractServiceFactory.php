<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Logger abstract service factory.
 *
 * Allow to configure multiple loggers for application.
 */
class LoggerAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string                  $name
     * @param  string                  $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if ('logger\\' != substr(strtolower($requestedName), 0, 7)) {
            return false;
        }

        $config  = $serviceLocator->get('Config');
        if (!isset($config['log'])) {
            return false;
        }

        $config  = array_change_key_case($config['log']);
        $service = substr(strtolower($requestedName), 7);

        return isset($config[$service]);
    }

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string                  $name
     * @param  string                  $requestedName
     * @return \Zend\Log\Logger
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config  = $serviceLocator->get('Config');
        $config  = array_change_key_case($config['log']);
        $service = substr(strtolower($requestedName), 7);

        return new Logger($config[$service]);
    }
}
