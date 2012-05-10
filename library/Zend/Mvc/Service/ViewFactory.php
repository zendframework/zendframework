<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\View\View;

class ViewFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $view = new View;
        $view->setEventManager($serviceLocator->get('EventManager'));
        $view->events()->attachAggregate($serviceLocator->get('ViewPhpRendererStrategy'));
        return $view;
    }
}
