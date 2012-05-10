<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Mvc\Controller\PluginLoader as ControllerPluginLoader;


class ControllerPluginLoaderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');
        $map    = (isset($config->controller) && isset($config->controller->map)) ? $config->controller->map: array();
        $loader = new ControllerPluginLoader($map);
        return $loader;
    }
}
