<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\View\HelperLoader as ViewHelperLoader;

class ViewHelperLoaderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');
        if (isset($config->view) && isset($config->view->helper_map)) {
            $map = $config->view->helper_map;
        } else {
            $map = array();
        }
        return new ViewHelperLoader($map);
    }
}
