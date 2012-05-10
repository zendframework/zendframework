<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\View\HelperBroker as ViewHelperBroker,
    Zend\View\Renderer\PhpRenderer as ViewPhpRenderer;

class ViewPhpRendererFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $resolver     = $serviceLocator->get('ViewAggregateResolver');
        $helperLoader = $serviceLocator->get('ViewHelperLoader');

        $config = $serviceLocator->get('Configuration');

        $broker       = new ViewHelperBroker();
        $broker->setClassLoader($helperLoader);

        $url          = $broker->load('url');
        $url->setRouter($serviceLocator->get('Router'));
        $basePath     = $broker->load('basePath');

        // set base path
        if (isset($config->view) && isset($config->view->base_path)) {
            $basePath->setBasePath($config->view->base_path);
        } else {
            $basePath->setBasePath('/');
        }

        $renderer     = new ViewPhpRenderer();
        $renderer->setBroker($broker);
        $renderer->setResolver($resolver);
        return $renderer;
    }
}
