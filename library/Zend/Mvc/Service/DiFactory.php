<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Di\Di,
    Zend\Di\Configuration as DiConfiguration,
    Zend\ServiceManager\ServiceManager,
    Zend\ServiceManager\Di\DiAbstractServiceFactory;

class DiFactory implements FactoryInterface
{
    protected $sharedEventManager = null;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $di = new Di();

        $config = $serviceLocator->get('Configuration');

        if (isset($config->di)) {
            $di->configure(new DiConfiguration($config->di));
        }

        if ($serviceLocator instanceof ServiceManager) {
            // register as abstract factory as well:
            /** @var $serviceLocator ServiceManager */
            $serviceLocator->addAbstractFactory(
                new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_BEFORE_DI)
            );
        }

        return $di;
    }
}