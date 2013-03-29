<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager\Proxy;

use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception;

/**
 * Service factory responsible of instantiating {@see \Zend\ServiceManager\Proxy\LazyServiceFactory}
 * and configuring it starting from application configuration
 */
class LazyServiceFactoryFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \Zend\ServiceManager\Proxy\LazyServiceFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['lazy_services'])) {
            throw new Exception\InvalidArgumentException('Missing "lazy_services" config key');
        }

        $lazyServicesConfig = $config['lazy_services'];

        if (!isset($lazyServicesConfig['map'])) {
            throw new Exception\InvalidArgumentException('Missing "map" config key in "lazy_services"');
        }

        $factoryConfig = new Configuration();

        if (isset($lazyServicesConfig['proxies_target_dir'])) {
            $factoryConfig->setProxiesTargetDir($lazyServicesConfig['proxies_target_dir']);
        }

        if (isset($lazyServicesConfig['auto_generate_proxies'])) {
            $factoryConfig->setAutoGenerateProxies($lazyServicesConfig['auto_generate_proxies']);

            // register the proxy autoloader if the proxies already exist
            if (!$lazyServicesConfig['auto_generate_proxies']) {
                spl_autoload_register($factoryConfig->getProxyAutoloader());
            }
        }

        if (isset($lazyServicesConfig['proxies_namespace'])) {
            $factoryConfig->setProxiesNamespace($lazyServicesConfig['proxies_namespace']);
        }

        return new LazyServiceFactory(
            new LazyLoadingValueHolderFactory($factoryConfig),
            $lazyServicesConfig['map']
        );
    }
}
