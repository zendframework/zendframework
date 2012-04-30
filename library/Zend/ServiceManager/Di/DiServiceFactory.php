<?php

namespace Zend\ServiceManager\Di;


use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceManager,
    Zend\ServiceManager\Exception,
    Zend\Di\Di,
    Zend\Di\Exception\ClassNotFoundException as DiClassNotFoundException;

class DiServiceFactory extends Di implements FactoryInterface
{
    const USE_SM_BEFORE_DI = 'before';
    const USE_SM_AFTER_DI  = 'after';
    const USE_SM_NONE      = 'none';

    protected $di = null;
    protected $name = null;
    protected $parameters = array();
    protected $useServiceManager = self::USE_SM_AFTER_DI;

    /**
     * @var ServiceManager
     */
    protected $serviceManager = null;


    public function __construct(Di $di, $name, array $parameters = array(), $useServiceManager = self::USE_SM_NONE)
    {
        $this->di = $di;
        $this->name = $name;
        $this->parameters = $parameters;
        if (in_array($useServiceManager, array(self::USE_SM_BEFORE_DI, self::USE_SM_AFTER_DI, self::USE_SM_NONE))) {
            $this->useServiceManager = $useServiceManager;
        }

        // since we are using this in a proxy-fashion, localize state
        $this->definitions = $this->di->definitions;
        $this->instanceManager = $this->di->instanceManager;
    }


    public function createService(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this->get($this->name, $this->parameters, true);
    }

    /**
     * Override, as we want it to use the functionality defined in the proxy
     *
     * @param  string $name
     * @param  array $params
     * @return object
     */
    public function get($name, array $params = array())
    {
        // allow this di service to get dependencies from the service locator BEFORE trying di
        if ($this->useServiceManager == self::USE_SM_BEFORE_DI && $this->serviceManager->has($name)) {
            return $this->serviceManager->get($name);
        }

        try {

            $service = parent::get($name, $params);
            return $service;

        } catch (DiClassNotFoundException $e) {

            // allow this di service to get dependencies from the service locator AFTER trying di
            if ($this->useServiceManager == self::USE_SM_AFTER_DI && $this->serviceManager->has($name)) {
                return $this->serviceManager->get($name);
            } else {
                throw new Exception\InvalidServiceNameException(
                    sprintf('Service %s was not found in this DI instance', $name),
                    null,
                    $e
                );
            }
        }

    }

}
