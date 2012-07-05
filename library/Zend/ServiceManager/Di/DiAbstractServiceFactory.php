<?php

namespace Zend\ServiceManager\Di;

use Zend\ServiceManager\AbstractFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Di\Di;

class DiAbstractServiceFactory extends DiServiceFactory implements AbstractFactoryInterface
{
    /**
     * @param \Zend\Di\Di $di
     * @param null|string|\Zend\Di\InstanceManager $useServiceLocator
     */
    public function __construct(Di $di, $useServiceLocator = self::USE_SL_NONE)
    {
        $this->di = $di;
        if (in_array($useServiceLocator, array(self::USE_SL_BEFORE_DI, self::USE_SL_AFTER_DI, self::USE_SL_NONE))) {
            $this->useServiceLocator = $useServiceLocator;
        }

        // since we are using this in a proxy-fashion, localize state
        $this->definitions = $this->di->definitions;
        $this->instanceManager = $this->di->instanceManager;
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $serviceName, $requestedName)
    {
        $this->serviceLocator = $serviceLocator;
        if ($requestedName) {
            return $this->get($requestedName, array(), true);
        } else {
            return $this->get($serviceName, array(), true);
        }

    }

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->instanceManager->hasSharedInstance($requestedName)
            || $this->instanceManager->hasAlias($requestedName)
            || $this->instanceManager->hasConfiguration($requestedName)
            || $this->instanceManager->hasTypePreferences($requestedName)
            || $this->definitions->hasClass($requestedName);
    }
}
