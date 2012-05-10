<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\View\Resolver\TemplatePathStack as ViewTemplatePathStack;

class ViewTemplatePathStackFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');
        if (isset($config->view) && isset($config->view->template_path_stack)) {
            $stack = $config->view->template_path_stack;
        } else {
            $stack = array();
        }
        return new ViewTemplatePathStack($stack);
    }
}
