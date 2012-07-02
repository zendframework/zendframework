<?php

namespace Zend\ServiceManager;

interface AbstractFactoryInterface
{
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName);
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName);
}
