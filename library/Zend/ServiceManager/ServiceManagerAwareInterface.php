<?php

namespace Zend\ServiceManager;

interface ServiceManagerAwareInterface
{
    public function setServiceManager(ServiceManager $serviceManager);
}
