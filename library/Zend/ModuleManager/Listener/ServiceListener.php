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
use Zend\ServiceManager\ServiceManager;
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
     * Default service manager used to fulfill other SMs that need to be lazy loaded
     *
     * @var ServiceManager
     */
    protected $defaultServiceManager;

    /**
     * @var array
     */
    protected $defaultServiceConfiguration;

    /**
     * @var array
     */
    protected $serviceManagers = array();

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager, $configuration = null)
    {
        $this->defaultServiceManager = $serviceManager;
        $this->defaultServiceConfiguration = $configuration;
    }

    /**
     * @param string $key
     * @param ServiceManager|string $serviceManager
     * @return ServiceListener
     */
    public function addServiceManager($serviceManager, $key, $moduleInterface, $method)
    {
        if (is_string($serviceManager)) {
            $smKey = $serviceManager;
        } elseif ($serviceManager instanceof ServiceManager) {
            $smKey = spl_object_hash($serviceManager);
        } else {
            throw new Exception\RuntimeException(sprintf(
                'Invalid service manager provided, expected ServiceManager or string, %s provided',
                (string) $serviceManager
            ));
        }
        $this->serviceManagers[$smKey] = array(
            'service_manager'        => $serviceManager,
            'config_key'             => $key,
            'module_class_interface' => $moduleInterface,
            'module_class_method'    => $method,
            'configuration'          => array(),
        );
        return $this;
    }

    /**
     * @param  EventManagerInterface $events
     * @return ServiceListener
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULE, array($this, 'onLoadModule'));
        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULES_POST, array($this, 'onLoadModulesPost'));
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

        foreach ($this->serviceManagers as $key => $sm) {

            if (!$module instanceof $sm['module_class_interface']
                && !method_exists($module, $sm['module_class_method'])
            ) {
                continue;
            }

            $config = $module->{$sm['module_class_method']}();

            if ($config instanceof ServiceConfiguration) {
                $config = $this->serviceConfigurationToArray($config);
            }

            if ($config instanceof Traversable) {
                $config = ArrayUtils::iteratorToArray($config);
            }

            if (!is_array($config)) {
                // If we don't have an array by this point, nothing left to do.
                continue;
            }

            // We're keeping track of which modules provided which configuration to which serivce managers.
            // The actual merging takes place later. Doing it this way will enable us to provide more powerful
            // debugging tools for showing which modules overrode what.
            $this->serviceManagers[$key]['configuration'][$e->getModuleName()] = $config;
        }
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

        if ($this->defaultServiceConfiguration) {
            $config = ArrayUtils::merge(array('service_manager' => $this->defaultServiceConfiguration), $config);
        }

        foreach ($this->serviceManagers as $key => $sm) {

            if (isset($config[$sm['config_key']])
                && is_array($config[$sm['config_key']])
                && !empty($config[$sm['config_key']])
            ) {

                $this->serviceManagers[$key]['configuration']['merged_config'] = $config[$sm['config_key']];
            }

            // Merge all of the things!
            $smConfig = array();
            foreach ($this->serviceManagers[$key]['configuration'] as $configs) {
                if (isset($configs['configuration_classes'])) {
                    foreach ($configs['configuration_classes'] as $class) {
                        $config = ArrayUtils::merge($configs, $this->serviceConfigurationToArray($class));
                    }
                }
                $smConfig = ArrayUtils::merge($smConfig, $configs);
            }

            if (!$sm['service_manager'] instanceof ServiceManager) {
                $instance = $this->defaultServiceManager->get($sm['service_manager']);
                if (!$instance instanceof ServiceManager) {
                    throw new Exception\RuntimeException(sprintf(
                        'Could not find a valid ServiceManager for %s',
                        $sm['service_manager']
                    ));
                }
                $sm['service_manager'] = $instance;
            }
            $serviceConfig = new ServiceConfiguration($smConfig);
            $serviceConfig->configureServiceManager($sm['service_manager']);
        }

        $this->configured = true;
    }

    /**
     * Merge a service configuration container
     *
     * Extracts the various service configuration arrays, and then merges with
     * the internal service configuration.
     *
     * @param  ServiceConfiguration|string $config Instance of ServiceConfiguration or class name
     * @return array
     */
    protected function serviceConfigurationToArray($config)
    {
        if (is_string($config) && class_exists($config)) {
            $class  = $config;
            $config = new $class;
        }

        if (!$config instanceof ServiceConfiguration) {
            throw new Exception\RuntimeException(sprintf(
                'Invalid service manager configuration class provided; received "%s", expected an instance of Zend\ServiceManager\Configuration',
                $class
            ));
        }

        return array(
            'abstract_factories' => $config->getAbstractFactories(),
            'aliases'            => $config->getAliases(),
            'initializers'       => $config->getInitializers(),
            'factories'          => $config->getFactories(),
            'invokables'         => $config->getInvokables(),
            'services'           => $config->getServices(),
            'shared'             => $config->getShared(),
        );
    }
}
