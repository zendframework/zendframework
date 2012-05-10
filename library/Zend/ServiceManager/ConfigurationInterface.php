<?php

namespace Zend\ServiceManager;

interface ConfigurationInterface
{
    public function configureServiceManager(ServiceManager $serviceManager);
}
