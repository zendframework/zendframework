<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class ConfigurationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mm           = $serviceLocator->get('ModuleManager');
        $mm->loadModules();
        $moduleParams = $mm->getEvent()->getParams();
        $config       = $moduleParams['configListener']->getMergedConfig();
        return $config;
    }
}