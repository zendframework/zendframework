<?php

namespace Zend\ServiceManager\Di;

use Zend\ServiceManager\AbstractFactoryInterface,
    Zend\ServiceManager\ServiceManager,
    Zend\Di\Di;

class DiAbstractServiceFactory extends DiServiceFactory implements AbstractFactoryInterface
{

    public function __construct(Di $di, $useServiceManager = self::USE_SM_NONE)
    {
        $this->di = $di;
        if (in_array($useServiceManager, array(self::USE_SM_BEFORE_DI, self::USE_SM_AFTER_DI, self::USE_SM_NONE))) {
            $this->useServiceManager = $useServiceManager;
        }

        // since we are using this in a proxy-fashion, localize state
        $this->definitions = $this->di->definitions;
        $this->instanceManager = $this->di->instanceManager;
    }


    public function createServiceWithName(ServiceManager $serviceManager, $serviceName, $requestedName = null)
    {
        $this->serviceManager = $serviceManager;
        if ($requestedName) {
            return $this->get($requestedName, array(), true);
        } else {
            return $this->get($serviceName, array(), true);
        }

    }

}
