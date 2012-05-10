<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Mvc\View\ExceptionStrategy as ViewExceptionStrategy;

class ViewExceptionStrategyFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config                = $serviceLocator->get('Configuration');
        $displayExceptions = ($config->view->display_exceptions) ?: false;
        $exceptionTemplate      = ($config->view->exception_template) ?: 'error';
        $strategy              = new ViewExceptionStrategy();
        $strategy->setDisplayExceptions($displayExceptions);
        $strategy->setExceptionTemplate($exceptionTemplate);
        return $strategy;
    }
}
