<?php

namespace Zend\ServiceManager;

use Zend\ServiceManager\ServiceLocatorInterface;

interface InitializerInterface
{
    public function initialize($instance, ServiceLocatorInterface $serviceLocator);
}
