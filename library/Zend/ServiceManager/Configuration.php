<?php

namespace Zend\ServiceManager;

class Configuration implements ConfigurationInterface
{
    protected $configuration = array();

    public function __construct($configuration = array())
    {
        $this->configuration = $configuration;
    }

    public function getAllowOverride()
    {
        return (isset($this->configuration['allow_override'])) ? $this->configuration['allow_override'] : null;
    }

    public function getFactories()
    {
        return (isset($this->configuration['factories'])) ? $this->configuration['factories'] : array();
    }

    public function getAbstractFactories()
    {
        return (isset($this->configuration['abstract_factories'])) ? $this->configuration['abstract_factories'] : array();
    }

    public function getInvokables()
    {
        return (isset($this->configuration['invokables'])) ? $this->configuration['invokables'] : array();
    }

    public function getServices()
    {
        return (isset($this->configuration['services'])) ? $this->configuration['services'] : array();
    }

    public function getAliases()
    {
        return (isset($this->configuration['aliases'])) ? $this->configuration['aliases'] : array();
    }

    public function getInitializers()
    {
        return (isset($this->configuration['initializers'])) ? $this->configuration['initializers'] : array();
    }

    public function getShared()
    {
        return (isset($this->configuration['shared'])) ? $this->configuration['shared'] : array();
    }

    public function configureServiceManager(ServiceManager $serviceManager)
    {
        $allowOverride = $this->getAllowOverride();
        isset($allowOverride) ? $serviceManager->setAllowOverride($allowOverride) : null;

        foreach ($this->getFactories() as $name => $factory) {
            $serviceManager->setFactory($name, $factory);
        }

        foreach ($this->getAbstractFactories() as $factory) {
            $serviceManager->addAbstractFactory($factory);
        }

        foreach ($this->getInvokables() as $name => $invokable) {
            $serviceManager->setInvokableClass($name, $invokable);
        }

        foreach ($this->getServices() as $name => $service) {
            $serviceManager->setService($name, $service);
        }

        foreach ($this->getAliases() as $alias => $nameOrAlias) {
            $serviceManager->setAlias($alias, $nameOrAlias);
        }

        foreach ($this->getInitializers() as $initializer) {
            $serviceManager->addInitializer($initializer);
        }

        foreach ($this->getShared() as $name => $isShared) {
            $serviceManager->setShared($name, $isShared);
        }
    }

}
