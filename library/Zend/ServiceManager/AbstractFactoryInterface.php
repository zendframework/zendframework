<?php

namespace Zend\ServiceManager;

interface AbstractFactoryInterface
{
    public function canCreateServiceWithName($name /*, $requestedName */);
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name /*, $requestedName */);
}
