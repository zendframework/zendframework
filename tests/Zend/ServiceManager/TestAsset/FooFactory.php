<?php

namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\FactoryInterface,
Zend\ServiceManager\ServiceLocatorInterface;

class FooFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Foo;
    }
}
