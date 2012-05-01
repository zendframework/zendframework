<?php

namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\AbstractFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class FooAbstractFactory implements AbstractFactoryInterface
{
    public function canCreateServiceWithName($name)
    {
    }
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name)
    {
        return new Foo;
    }
}
