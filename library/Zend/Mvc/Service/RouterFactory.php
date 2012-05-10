<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Mvc\Router\Http\TreeRouteStack as Router;

class RouterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');
        $routes = $config->routes ?: array();
        $router = Router::factory($routes);
        return $router;
    }
}
