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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ModuleManagerFactory implements FactoryInterface
{
    /**
     * Default mvc-related service configuration -- can be overridden by modules.
     *
     * @var array
     */
    protected $defaultServiceConfiguration = array(
        'invokables' => array(
            'DispatchListener' => 'Zend\Mvc\DispatchListener',
            'Request'          => 'Zend\Http\PhpEnvironment\Request',
            'Response'         => 'Zend\Http\PhpEnvironment\Response',
            'RouteListener'    => 'Zend\Mvc\RouteListener',
            'ViewManager'      => 'Zend\Mvc\View\ViewManager',
        ),
        'factories' => array(
            'Application'             => 'Zend\Mvc\Service\ApplicationFactory',
            'Configuration'           => 'Zend\Mvc\Service\ConfigurationFactory',
            'ControllerLoader'        => 'Zend\Mvc\Service\ControllerLoaderFactory',
            'ControllerPluginManager' => 'Zend\Mvc\Service\ControllerPluginManagerFactory',
            'DependencyInjector'      => 'Zend\Mvc\Service\DiFactory',
            'Router'                  => 'Zend\Mvc\Service\RouterFactory',
            'ViewHelperManager'       => 'Zend\Mvc\Service\ViewHelperManagerFactory',
            'ViewFeedRenderer'        => 'Zend\Mvc\Service\ViewFeedRendererFactory',
            'ViewFeedStrategy'        => 'Zend\Mvc\Service\ViewFeedStrategyFactory',
            'ViewJsonRenderer'        => 'Zend\Mvc\Service\ViewJsonRendererFactory',
            'ViewJsonStrategy'        => 'Zend\Mvc\Service\ViewJsonStrategyFactory',
        ),
        'aliases' => array(
            'Config'                            => 'Configuration',
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
     * from the "module_listener_options" key of the ApplicationConfiguration
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
        $configuration    = $serviceLocator->get('ApplicationConfiguration');
        $listenerOptions  = new ListenerOptions($configuration['module_listener_options']);
        $defaultListeners = new DefaultListenerAggregate($listenerOptions);
        $serviceListener  = new ServiceListener($serviceLocator, $this->defaultServiceConfiguration);

        $serviceListener->addServiceManager($serviceLocator, 'service_manager', 'Zend\ModuleManager\Feature\ServiceProviderInterface', 'getServiceConfiguration');
        $serviceListener->addServiceManager('ControllerLoader', 'controllers', 'Zend\ModuleManager\Feature\ControllerProviderInterface', 'getControllerConfiguration');
        $serviceListener->addServiceManager('ControllerPluginManager', 'controller_plugins', 'Zend\ModuleManager\Feature\ControllerPluginProviderInterface', 'getControllerPluginConfiguration');
        $serviceListener->addServiceManager('ViewHelperManager', 'view_helpers', 'Zend\ModuleManager\Feature\ViewHelperProviderInterface', 'getViewHelperConfiguration');

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
