<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Mvc\Application;

class ApplicationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Application($serviceLocator->get('Configuration'), $serviceLocator);
    }
}