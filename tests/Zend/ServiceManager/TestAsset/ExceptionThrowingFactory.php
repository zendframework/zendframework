<?php

namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\FactoryInterface,
Zend\ServiceManager\ServiceLocatorInterface;

class ExceptionThrowingFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        throw new FooException("A");
        return new Foo;
    }
}
