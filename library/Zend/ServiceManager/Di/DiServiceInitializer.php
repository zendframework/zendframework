<?php

namespace Zend\ServiceManager\Di;


use Zend\ServiceManager\InitializerInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\Exception,
    Zend\Di\Di,
    Zend\Di\Exception\ClassNotFoundException as DiClassNotFoundException;

class DiServiceInitializer extends Di implements InitializerInterface
{

    protected $di = null;
    protected $diInstanceManagerProxy = null;
    protected $serviceLocator = null;

    public function __construct(Di $di, ServiceLocatorInterface $serviceLocator, DiInstanceManagerProxy $diImProxy = null)
    {
        $this->di = $di;
        $this->serviceLocator = $serviceLocator;
        $this->diInstanceManagerProxy = ($diImProxy) ?: new DiInstanceManagerProxy($di->instanceManager(), $serviceLocator);
    }

    public function initialize($instance)
    {
        $instanceManager = $this->di->instanceManager;
        $this->di->instanceManager = $this->diInstanceManagerProxy;
        try {
            $this->di->injectDependencies($instance);
            $this->di->instanceManager = $instanceManager;
        } catch (Exception $e) {
            $this->di->instanceManager = $instanceManager;
            throw $e;
        }
    }

}
