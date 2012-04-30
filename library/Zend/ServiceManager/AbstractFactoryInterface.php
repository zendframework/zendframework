<?php

namespace Zend\ServiceManager;

interface AbstractFactoryInterface
{
    public function createServiceWithName(ServiceManager $serviceManager, $name);
}