<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\ServiceManager\ConfigurationInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ServiceManagerConfiguration implements ConfigurationInterface
{
    /**
     * Services that can be instantiated without factories
     * 
     * @var array
     */
    protected $services = array(
        'DispatchListener' => 'Zend\Mvc\DispatchListener',
        'Request'          => 'Zend\Http\PhpEnvironment\Request',
        'Response'         => 'Zend\Http\PhpEnvironment\Response',
        'RouteListener'    => 'Zend\Mvc\RouteListener',
        'ViewManager'      => 'Zend\Mvc\View\ViewManager',
    );

    /**
     * Service factories
     * 
     * @var array
     */
    protected $factories = array(
        'Application'            => 'Zend\Mvc\Service\ApplicationFactory',
        'Configuration'          => 'Zend\Mvc\Service\ConfigurationFactory',
        'ControllerLoader'       => 'Zend\Mvc\Service\ControllerLoaderFactory',
        'ControllerPluginBroker' => 'Zend\Mvc\Service\ControllerPluginBrokerFactory',
        'ControllerPluginLoader' => 'Zend\Mvc\Service\ControllerPluginLoaderFactory',
        'DependencyInjector'     => 'Zend\Mvc\Service\DiFactory',
        'EventManager'           => 'Zend\Mvc\Service\EventManagerFactory',
        'ModuleManager'          => 'Zend\Mvc\Service\ModuleManagerFactory',
        'Router'                 => 'Zend\Mvc\Service\RouterFactory',
        'ViewFeedRenderer'       => 'Zend\Mvc\Service\ViewFeedRendererFactory',
        'ViewFeedStrategy'       => 'Zend\Mvc\Service\ViewFeedStrategyFactory',
        'ViewJsonRenderer'       => 'Zend\Mvc\Service\ViewJsonRendererFactory',
        'ViewJsonStrategy'       => 'Zend\Mvc\Service\ViewJsonStrategyFactory',
    );

    /**
     * Abstract factories
     * 
     * @var array
     */
    protected $abstractFactories = array();

    /**
     * Aliases
     * 
     * @var array
     */
    protected $aliases = array(
        'Config'                                  => 'Configuration',
        'Di'                                      => 'DependencyInjector',
        'Zend\Di\LocatorInterface'                => 'DependencyInjector',
        'Zend\EventManager\EventManagerInterface' => 'EventManager',
        'Zend\Mvc\Controller\PluginLoader'        => 'ControllerPluginLoader',
        'Zend\Mvc\Controller\PluginBroker'        => 'ControllerPluginBroker',
    );

    /**
     * Shared services
     *
     * Services are shared by default; this is primarily to indicate services
     * that should NOT be shared
     * 
     * @var array
     */
    protected $shared = array(
        'EventManager' => false
    );

    /**
     * Constructor
     *
     * Merges internal arrays with those passed via configuration
     * 
     * @param  array $configuration 
     * @return void
     */
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

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
     *
     * In addition to using each of the internal properties to configure the
     * service manager, also adds an initializer to inject ServiceManagerAware
     * classes with the service manager.
     *
     * @param  ServiceManager $serviceManager 
     * @return void
     */
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
            if ($instance instanceof EventManagerAwareInterface) {
                $instance->setEventManager($serviceManager->get('EventManager'));
            }
        });

        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof ServiceManagerAwareInterface) {
                $instance->setServiceManager($instance);
            }
        });

        $serviceManager->setService('ServiceManager', $serviceManager);
        $serviceManager->setAlias('Zend\ServiceManager\ServiceLocatorInterface', 'ServiceManager');
        $serviceManager->setAlias('Zend\ServiceManager\ServiceManager', 'ServiceManager');
    }
}
