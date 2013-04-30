<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Service;

use Zend\Cache\StorageFactory;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Storage cache factory for multiple caches.
 */
class StorageCacheAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string                  $name
     * @param  string                  $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if ('cache\\' !== substr(strtolower($requestedName), 0, 6)) {
            return false;
        }

        $config  = $serviceLocator->get('Config');
        if (!isset($config['caches'])) {
            return false;
        }

        $config  = array_change_key_case($config['caches']);
        $service = substr(strtolower($requestedName), 6);

        return isset($config[$service]) && is_array($config[$service]);
    }

    /**
     * @param  ServiceLocatorInterface              $serviceLocator
     * @param  string                               $name
     * @param  string                               $requestedName
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config      = $serviceLocator->get('Config');
        $config      = array_change_key_case($config['caches']);
        $service     = substr(strtolower($requestedName), 6);
        $cacheConfig = $config[$service];

        return StorageFactory::factory($cacheConfig);
    }
}
