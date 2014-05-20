<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class ServiceManagerConfig extends Config
{
    /**
     * Services that can be instantiated without factories
     *
     * @var array
     */
    protected $invokables = array(
        'SharedEventManager' => 'Zend\EventManager\SharedEventManager',
    );

    /**
     * Service factories
     *
     * @var array
     */
    protected $factories = array(
        'EventManager'  => 'Zend\Mvc\Service\EventManagerFactory',
        'ModuleManager' => 'Zend\Mvc\Service\ModuleManagerFactory',
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
        'Zend\EventManager\EventManagerInterface'     => 'EventManager',
        'Zend\ServiceManager\ServiceLocatorInterface' => 'ServiceManager',
        'Zend\ServiceManager\ServiceManager'          => 'ServiceManager',
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
        'EventManager' => false,
    );

    /**
     * Delegators
     *
     * @var array
     */
    protected $delegators = array();

    /**
     * Initializers
     *
     * @var array
     */
    protected $initializers = array();

    /**
     * Constructor
     *
     * Merges internal arrays with those passed via configuration
     *
     * @param  array $configuration
     */
    public function __construct(array $configuration = array())
    {
        $this->initializers = array(
            'EventManagerAwareInitializer' => function ($instance, ServiceManager $serviceManager) {
                if ($instance instanceof EventManagerAwareInterface) {
                    $eventManager = $instance->getEventManager();

                    if ($eventManager instanceof EventManagerInterface) {
                        $eventManager->setSharedManager($serviceManager->get('SharedEventManager'));
                    } else {
                        $instance->setEventManager($serviceManager->get('EventManager'));
                    }
                }
            },
        );

        $configuration = array_replace_recursive(array(
            'invokables'         => $this->invokables,
            'factories'          => $this->factories,
            'abstract_factories' => $this->abstractFactories,
            'aliases'            => $this->aliases,
            'shared'             => $this->shared,
            'delegators'         => $this->delegators,
            'initializers'       => $this->initializers,
        ), $configuration);

        parent::__construct($configuration);
    }

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
     *
     * In addition to using each of the internal properties to configure the
     * service manager, also adds an initializer to inject ServiceManagerAware
     * and ServiceLocatorAware classes with the service manager.
     *
     * @param  ServiceManager $serviceManager
     * @return void
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        parent::configureServiceManager($serviceManager);

        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof ServiceManagerAwareInterface) {
                $instance->setServiceManager($serviceManager);
            }
        });

        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof ServiceLocatorAwareInterface) {
                $instance->setServiceLocator($serviceManager);
            }
        });

        $serviceManager->setService('ServiceManager', $serviceManager);
    }
}
