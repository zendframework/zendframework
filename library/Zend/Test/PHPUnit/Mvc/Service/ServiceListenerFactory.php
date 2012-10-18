<?php

namespace Zend\Test\PHPUnit\Mvc\Service;

use Zend\Mvc\Service\ServiceListenerFactory as BaseServiceListenerFactory;

class ServiceListenerFactory extends BaseServiceListenerFactory
{
    protected $defaultServiceConfig = array(
        'invokables' => array(
            'DispatchListener' => 'Zend\Mvc\DispatchListener',
            'RouteListener'    => 'Zend\Mvc\RouteListener',
            'Request'          => 'Zend\Http\PhpEnvironment\Request', // override
            'Response'         => 'Zend\Http\PhpEnvironment\Response', // override
            'ViewManager'      => 'Zend\Mvc\View\Http\ViewManager', // override
        ),
        'factories' => array(
            'Application'             => 'Zend\Mvc\Service\ApplicationFactory',
            'Config'                  => 'Zend\Mvc\Service\ConfigFactory',
            'ControllerLoader'        => 'Zend\Mvc\Service\ControllerLoaderFactory',
            'ControllerPluginManager' => 'Zend\Mvc\Service\ControllerPluginManagerFactory',
            'ConsoleAdapter'          => 'Zend\Mvc\Service\ConsoleAdapterFactory',
            'ConsoleRouter'           => 'Zend\Mvc\Service\RouterFactory',
            'DependencyInjector'      => 'Zend\Mvc\Service\DiFactory',
            'HttpRouter'              => 'Zend\Mvc\Service\RouterFactory',
            'Router'                  => 'Zend\Test\PHPUnit\Mvc\Service\RouterFactory', // override
            'ViewHelperManager'       => 'Zend\Mvc\Service\ViewHelperManagerFactory',
            'ViewFeedRenderer'        => 'Zend\Mvc\Service\ViewFeedRendererFactory',
            'ViewFeedStrategy'        => 'Zend\Mvc\Service\ViewFeedStrategyFactory',
            'ViewJsonRenderer'        => 'Zend\Mvc\Service\ViewJsonRendererFactory',
            'ViewJsonStrategy'        => 'Zend\Mvc\Service\ViewJsonStrategyFactory',
            'ViewResolver'            => 'Zend\Mvc\Service\ViewResolverFactory',
            'ViewTemplateMapResolver' => 'Zend\Mvc\Service\ViewTemplateMapResolverFactory',
            'ViewTemplatePathStack'   => 'Zend\Mvc\Service\ViewTemplatePathStackFactory',
        ),
        'aliases' => array(
            'Configuration'                          => 'Config',
            'Console'                                => 'ConsoleAdapter',
            'ControllerPluginBroker'                 => 'ControllerPluginManager',
            'Di'                                     => 'DependencyInjector',
            'Zend\Di\LocatorInterface'               => 'DependencyInjector',
            'Zend\Mvc\Controller\PluginBroker'       => 'ControllerPluginBroker',
            'Zend\Mvc\Controller\PluginManager'      => 'ControllerPluginManager',
            'Zend\View\Resolver\TemplateMapResolver' => 'ViewTemplateMapResolver',
            'Zend\View\Resolver\TemplatePathStack'   => 'ViewTemplatePathStack',
            'Zend\View\Resolver\AggregateResolver'   => 'ViewResolver',
            'Zend\View\Resolver\ResolverInterface'   => 'ViewResolver',
        ),
    );
}
