<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Mvc\View\RouteNotFoundStrategy as ViewRouteNotFoundStrategy;

class ViewRouteNotFoundStrategyFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config                = $serviceLocator->get('Configuration');
        $displayNotFoundReason = ($config->view->display_not_found_reason) ?: false;
        $notFoundTemplate      = ($config->view->not_found_template) ?: '404';
        $strategy              = new ViewRouteNotFoundStrategy();
        $strategy->setDisplayNotFoundReason($displayNotFoundReason);
        $strategy->setNotFoundTemplate($notFoundTemplate);
        return $strategy;
    }
}




