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
 * @package    Zend_ModuleManager
 * @subpackage Listener
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\ModuleManager\Listener;

use Traversable;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ServiceManager\Configuration as ServiceConfiguration;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ServiceListener implements ListenerAggregateInterface
{
    /**
     * @var bool
     */
    protected $configured = false;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Service configuration
     * 
     * @var array
     */
    protected $serviceConfig = array(
        'abstract_factories' => array(),
        'aliases'            => array(),
        'factories'          => array(),
        'invokables'         => array(),
        'services'           => array(),
        'shared'             => array(),
    );

    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * @param  ServiceLocatorInterface $services 
     * @return void
     */
    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services = $services;
    }

    /**
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('loadModule', array($this, 'onLoadModule'), 1500);
        $this->listeners[] = $events->attach('loadModules.post', array($this, 'onLoadModulesPost'), 8500);
        return $this;
    }

    /**
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $key => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$key]);
            }
        }
    }

    /**
     * Retrieve service manager configuration from module, and 
     * configure the service manager.
     *
     * If the module does not implement ServiceProviderInterface and does not
     * implement the "getServiceConfiguration()" method, does nothing. Also,
     * if the return value of that method is not a ServiceConfiguration object,
     * or not an array or Traversable that can seed one, does nothing.
     * 
     * @param  ModuleEvent $e 
     * @return void
     */
    public function onLoadModule(ModuleEvent $e)
    {
        $module = $e->getModule();
        if (!$module instanceof ServiceProviderInterface
            && !method_exists($module, 'getServiceConfiguration')
        ) {
            return;
        }

        $config = $module->getServiceConfiguration();

        if ($config instanceof ServiceConfiguration) {
            $this->mergeServiceConfiguration($config);
            return;
        }

        if ($config instanceof Traversable) {
            $config = ArrayUtils::iteratorToArray($config);
        }

        if (!is_array($config)) {
            // If we don't have an array by this point, nothing left to do.
            return;
        }

        $this->serviceConfig = ArrayUtils::merge($this->serviceConfig, $config);
    }

    /**
     * Use merged configuration to configure service manager
     *
     * If the merged configuration has a non-empty, array 'service_manager' 
     * key, it will be passed to a ServiceManager Configuration object, and
     * used to configure the service manager.
     * 
     * @param  ModuleEvent $e 
     * @return void
     */
    public function onLoadModulesPost(ModuleEvent $e)
    {
        $configListener = $e->getConfigListener();
        $config         = $configListener->getMergedConfig(false);
        if (isset($config['service_manager'])
            && is_array($config['service_manager'])
            && !empty($config['service_manager'])
        ) {
            $this->serviceConfig = ArrayUtils::merge($this->serviceConfig, $config['service_manager']);
        }

        $this->configureServiceManager();
    }

    /**
     * Configure the service manager
     *
     * Configures the service manager based on the internal, merged
     * service configuration.
     * 
     * @return void
     */
    public function configureServiceManager()
    {
        if ($this->configured) {
            // Don't configure twice
            return;
        }
        $serviceConfig = new ServiceConfiguration($this->serviceConfig);
        $serviceConfig->configureServiceManager($this->services);
        $this->configured = true;
    }

    /**
     * Merge a service configuration container
     *
     * Extracts the various service configuration arrays, and then merges with
     * the internal service configuration.
     * 
     * @param  ServiceConfiguration $config 
     * @return void
     */
    protected function mergeServiceConfiguration(ServiceConfiguration $config)
    {
        $serviceConfig = array(
            'abstract_factories' => $config->getAbstractFactories(),
            'aliases'            => $config->getAliases(),
            'factories'          => $config->getFactories(),
            'invokables'         => $config->getInvokables(),
            'services'           => $config->getServices(),
            'shared'             => $config->getShared(),
        );
        $this->serviceConfig = ArrayUtils::merge($this->serviceConfig, $serviceConfig);
    }
}
