<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\View\Resolver\TemplateMapResolver as ViewTemplateMapResolver;

class ViewTemplateMapResolverFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');
        if (isset($config->view) && isset($config->view->template_map)) {
            $map = $config->view->template_map;
        } else {
            $map = array();
        }
        return new ViewTemplateMapResolver($map);
    }
}
