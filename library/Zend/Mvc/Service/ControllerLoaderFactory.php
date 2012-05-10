<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\ServiceManager,
    Zend\ServiceManager\Di\DiServiceInitializer,
    Zend\Loader\Pluggable,
    Zend\View\View;

class ControllerLoaderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ServiceManager) {
            /** @var $controllerLoader ServiceManager */
            $controllerLoader = $serviceLocator->createScopedServiceManager();
            $configuration = $serviceLocator->get('Configuration');
            foreach ($configuration->controllers as $name => $controller) {
                $controllerLoader->setInvokableClass($name, $controller);
            }
            $controllerLoader->addInitializer(new DiServiceInitializer($serviceLocator->get('Di'), $serviceLocator));
            $controllerLoader->addInitializer(function ($instance) use ($serviceLocator) {
                if ($instance instanceof Pluggable) {
                    $instance->setBroker($serviceLocator->get('ControllerPluginBroker'));
                }
            });
            return $controllerLoader;
        } else {
            return $serviceLocator;
        }
    }
}
