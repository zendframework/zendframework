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
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

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
        if ($config instanceof Traversable) {
            $config = ArrayUtils::iteratorToArray($config);
        }
        if (is_array($config)) {
            $config = new ServiceConfiguration($config);
        }

        if (!$config instanceof ServiceConfiguration) {
            return;
        }

        $config->configureServiceManager($this->services);
    }
}
