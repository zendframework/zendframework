<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Mvc\Controller\PluginBroker as ControllerPluginBroker;


class ControllerPluginBrokerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $broker = new ControllerPluginBroker();
        $broker->setClassLoader($serviceLocator->get('ControllerPluginLoader'));
        return $broker;
    }
}
