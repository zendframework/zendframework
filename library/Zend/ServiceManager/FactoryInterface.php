<?php

namespace Zend\ServiceManager;

interface FactoryInterface
{
    public function createService(ServiceManager $serviceManager);
}