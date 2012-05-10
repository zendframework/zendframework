<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\ConfigurationInterface,
    Zend\ServiceManager\ServiceManager,
    Zend\ServiceManager\ServiceManagerAwareInterface;

class ServiceManagerConfiguration implements ConfigurationInterface
{

    protected $services = array(
        'Request'                      => 'Zend\Http\PhpEnvironment\Request',
        'Response'                     => 'Zend\Http\PhpEnvironment\Response',
        'RouteListener'                => 'Zend\Mvc\RouteListener',
        'DispatchListener'             => 'Zend\Mvc\DispatchListener'
    );

    protected $factories = array(
        'EventManager'                 => 'Zend\Mvc\Service\EventManagerFactory',
        'ModuleManager'                => 'Zend\Mvc\Service\ModuleManagerFactory',
        'Configuration'                => 'Zend\Mvc\Service\ConfigurationFactory',
        'Router'                       => 'Zend\Mvc\Service\RouterFactory',
        'ControllerPluginLoader'       => 'Zend\Mvc\Service\ControllerPluginLoaderFactory',
        'ControllerPluginBroker'       => 'Zend\Mvc\Service\ControllerPluginBrokerFactory',
        'Application'                  => 'Zend\Mvc\Service\ApplicationFactory',
        'DependencyInjector'           => 'Zend\Mvc\Service\DiFactory',
        'ControllerLoader'             => 'Zend\Mvc\Service\ControllerLoaderFactory',

        // view related stuffs
        'View'                         => 'Zend\Mvc\Service\ViewFactory',
        'ViewAggregateResolver'        => 'Zend\Mvc\Service\ViewAggregateResolverFactory',
        'ViewDefaultRenderingStrategy' => 'Zend\Mvc\Service\ViewDefaultRenderingStrategyFactory',
        'ViewExceptionStrategy'        => 'Zend\Mvc\Service\ViewExceptionStrategyFactory',
        'ViewHelperLoader'             => 'Zend\Mvc\Service\ViewHelperLoaderFactory',
        'ViewPhpRenderer'              => 'Zend\Mvc\Service\ViewPhpRendererFactory',
        'ViewPhpRendererStrategy'      => 'Zend\Mvc\Service\ViewPhpRendererStrategyFactory',
        'ViewRouteNotFoundStrategy'    => 'Zend\Mvc\Service\ViewRouteNotFoundStrategyFactory',
        'ViewTemplateMapResolver'      => 'Zend\Mvc\Service\ViewTemplateMapResolverFactory',
        'ViewTemplatePathStack'        => 'Zend\Mvc\Service\ViewTemplatePathStackFactory',
    );

    protected $abstractFactories = array(

    );

    protected $aliases = array(
        'EM' => 'EventManager',
        'Zend\EventManager\EventManagerInterface' => 'EventManager',
        'Zend\Di\LocatorInterface' => 'DependencyInjector',

        'MM'     => 'ModuleManager',
        'Config' => 'Configuration',
        'Di'     => 'DependencyInjector',
    );

    protected $shared = array(
        'EventManager' => false
    );

    public function __construct(array $configuration = array())
    {
        if (isset($configuration['services'])) {
            $this->services = array_merge($this->services, $configuration['services']);
        }

        if (isset($configuration['factories'])) {
            $this->factories = array_merge($this->factories, $configuration['factories']);
        }

        if (isset($configuration['abstract_factories'])) {
            $this->abstractFactories = array_merge($this->abstractFactories, $configuration['abstract_factories']);
        }

        if (isset($configuration['aliases'])) {
            $this->aliases = array_merge($this->aliases, $configuration['aliases']);
        }

        if (isset($configuration['shared'])) {
            $this->shared = array_merge($this->shared, $configuration['shared']);
        }

    }

    public function configureServiceManager(ServiceManager $serviceManager)
    {
        foreach ($this->services as $name => $service) {
            $serviceManager->setInvokableClass($name, $service);
        }

        foreach ($this->factories as $name => $factoryClass) {
            $serviceManager->setFactory($name, $factoryClass);
        }

        foreach ($this->abstractFactories as $factoryClass) {
            $serviceManager->addAbstractFactory($factoryClass);
        }

        foreach ($this->aliases as $name => $service) {
            $serviceManager->setAlias($name, $service);
        }

        foreach ($this->shared as $name => $value) {
            $serviceManager->setShared($name, $value);
        }

        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof ServiceManagerAwareInterface) {
                $instance->setServiceManager($instance);
            }
        });

        $serviceManager->setService('ServiceManager', $serviceManager);
        $serviceManager->setAlias('Zend\ServiceManager\ServiceManager', 'ServiceManager');
    }

}