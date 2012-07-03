<?php


namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\AbstractFactoryInterface,
Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class used to try to simulate a cyclic dependency in ServiceManager.
 */
class CircularDependencyAbstractFactory implements AbstractFactoryInterface
{
    public $expectedInstance = 'a retrieved value';

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if ($serviceLocator->has($name)) {
            return $serviceLocator->get($name);
        }

        return $this->expectedInstance;
    }
}
