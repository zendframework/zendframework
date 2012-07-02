<?php

namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\AbstractFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class FooAbstractFactory implements AbstractFactoryInterface
{
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
    }
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return new Foo;
    }
}
