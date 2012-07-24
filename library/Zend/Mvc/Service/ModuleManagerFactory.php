<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Service;

use Zend\ModuleManager\Listener\DefaultListenerAggregate;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\ModuleManager\Listener\ServiceListener;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class ModuleManagerFactory implements FactoryInterface
{
    /**
     * Default mvc-related service configuration -- can be overridden by modules.
     *
     * @var array
     */
    protected $defaultServiceConfig = array(
        'invokables' => array(
            'DispatchListener' => 'Zend\Mvc\DispatchListener',
            'RouteListener'    => 'Zend\Mvc\RouteListener',
        ),
        'factories' => array(
            'Application'             => 'Zend\Mvc\Service\ApplicationFactory',
            'Config'                  => 'Zend\Mvc\Service\ConfigFactory',
            'ControllerLoader'        => 'Zend\Mvc\Service\ControllerLoaderFactory',
            'ControllerPluginManager' => 'Zend\Mvc\Service\ControllerPluginManagerFactory',
            'ConsoleAdapter'          => 'Zend\Mvc\Service\ConsoleAdapterFactory',
            'DependencyInjector'      => 'Zend\Mvc\Service\DiFactory',
            'Request'                 => 'Zend\Mvc\Service\RequestFactory',
            'Response'                => 'Zend\Mvc\Service\ResponseFactory',
            'Router'                  => 'Zend\Mvc\Service\RouterFactory',
            'HttpRouter'              => 'Zend\Mvc\Service\RouterFactory',
            'ConsoleRouter'           => 'Zend\Mvc\Service\RouterFactory',
            'ViewManager'             => 'Zend\Mvc\Service\ViewManagerFactory',
            'ViewHelperManager'       => 'Zend\Mvc\Service\ViewHelperManagerFactory',
            'ViewFeedRenderer'        => 'Zend\Mvc\Service\ViewFeedRendererFactory',
            'ViewFeedStrategy'        => 'Zend\Mvc\Service\ViewFeedStrategyFactory',
            'ViewJsonRenderer'        => 'Zend\Mvc\Service\ViewJsonRendererFactory',
            'ViewJsonStrategy'        => 'Zend\Mvc\Service\ViewJsonStrategyFactory',
        ),
        'aliases' => array(
            'Console'                           => 'ConsoleAdapter',
            'Configuration'                     => 'Config',
            'ControllerPluginBroker'            => 'ControllerPluginManager',
            'Di'                                => 'DependencyInjector',
            'Zend\Di\LocatorInterface'          => 'DependencyInjector',
            'Zend\Mvc\Controller\PluginBroker'  => 'ControllerPluginBroker',
            'Zend\Mvc\Controller\PluginManager' => 'ControllerPluginManager',
        ),
    );

    /**
     * Creates and returns the module manager
     *
     * Instantiates the default module listeners, providing them configuration
     * from the "module_listener_options" key of the ApplicationConfig
     * service. Also sets the default config glob path.
     *
     * Module manager is instantiated and provided with an EventManager, to which
     * the default listener aggregate is attached. The ModuleEvent is also created
     * and attached to the module manager.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ModuleManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configuration    = $serviceLocator->get('ApplicationConfig');
        $listenerOptions  = new ListenerOptions($configuration['module_listener_options']);
        $defaultListeners = new DefaultListenerAggregate($listenerOptions);
        $serviceListener  = new ServiceListener($serviceLocator, $this->defaultServiceConfig);

        $serviceListener->addServiceManager($serviceLocator, 'service_manager', 'Zend\ModuleManager\Feature\ServiceProviderInterface', 'getServiceConfig');
        $serviceListener->addServiceManager('ControllerLoader', 'controllers', 'Zend\ModuleManager\Feature\ControllerProviderInterface', 'getControllerConfig');
        $serviceListener->addServiceManager('ControllerPluginManager', 'controller_plugins', 'Zend\ModuleManager\Feature\ControllerPluginProviderInterface', 'getControllerPluginConfig');
        $serviceListener->addServiceManager('ViewHelperManager', 'view_helpers', 'Zend\ModuleManager\Feature\ViewHelperProviderInterface', 'getViewHelperConfig');

        $events        = $serviceLocator->get('EventManager');
        $events->attach($defaultListeners);
        $events->attach($serviceListener);

        $moduleEvent   = new ModuleEvent;
        $moduleEvent->setParam('ServiceManager', $serviceLocator);

        $moduleManager = new ModuleManager($configuration['modules'], $events);
        $moduleManager->setEvent($moduleEvent);

        return $moduleManager;
    }
}
