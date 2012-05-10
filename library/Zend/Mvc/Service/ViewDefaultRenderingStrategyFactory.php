<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Mvc\View\DefaultRenderingStrategy as ViewDefaultRenderingStrategy;

class ViewDefaultRenderingStrategyFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config   = $serviceLocator->get('Configuration');
        $strategy = new ViewDefaultRenderingStrategy($serviceLocator->get('View'));
        $layout   = (isset($config->view) && isset($config->view->layout)) ? $config->view->layout : 'layout/layout';
        $strategy->setLayoutTemplate($layout);
        return $strategy;
    }
}
