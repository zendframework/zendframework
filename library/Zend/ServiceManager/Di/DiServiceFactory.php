<?php

namespace Zend\ServiceManager\Di;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\Exception,
    Zend\Di\Di,
    Zend\Di\Exception\ClassNotFoundException as DiClassNotFoundException;

class DiServiceFactory extends Di implements FactoryInterface
{
    /**@#+
     * constants
     */
    const USE_SL_BEFORE_DI = 'before';
    const USE_SL_AFTER_DI  = 'after';
    const USE_SL_NONE      = 'none';
    /**@#-*/

    /**
     * @var \Zend\Di\Di
     */
    protected $di = null;

    /**
     * @var \Zend\Di\InstanceManager
     */
    protected $name = null;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var string
     */
    protected $useServiceLocator = self::USE_SL_AFTER_DI;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * @param \Zend\Di\Di $di
     * @param null|\Zend\Di\InstanceManager $name
     * @param array $parameters
     * @param string $useServiceLocator
     */
    public function __construct(Di $di, $name, array $parameters = array(), $useServiceLocator = self::USE_SL_NONE)
    {
        $this->di = $di;
        $this->name = $name;
        $this->parameters = $parameters;
        if (in_array($useServiceLocator, array(self::USE_SL_BEFORE_DI, self::USE_SL_AFTER_DI, self::USE_SL_NONE))) {
            $this->useServiceLocator = $useServiceLocator;
        }

        // since we are using this in a proxy-fashion, localize state
        $this->definitions = $this->di->definitions;
        $this->instanceManager = $this->di->instanceManager;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return object
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this->get($this->name, $this->parameters, true);
    }

    /**
     * Override, as we want it to use the functionality defined in the proxy
     *
     * @param string $name
     * @param array $params
     * @return object
     * @throws Exception\InvalidServiceNameException
     */
    public function get($name, array $params = array())
    {
        // allow this di service to get dependencies from the service locator BEFORE trying di
        if ($this->useServiceLocator == self::USE_SL_BEFORE_DI && $this->serviceLocator->has($name)) {
            return $this->serviceLocator->get($name);
        }

        try {

            $service = parent::get($name, $params);
            return $service;

        } catch (DiClassNotFoundException $e) {

            // allow this di service to get dependencies from the service locator AFTER trying di
            if ($this->useServiceLocator == self::USE_SL_AFTER_DI && $this->serviceLocator->has($name)) {
                return $this->serviceLocator->get($name);
            } else {
                throw new Exception\ServiceNotFoundException(
                    sprintf('Service %s was not found in this DI instance', $name),
                    null,
                    $e
                );
            }
        }

    }

}
