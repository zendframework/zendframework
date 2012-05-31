<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace Zend\ModuleManager\Listener;

use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\ModuleManager\ModuleEvent;

/**
 * Locator registration listener
 * 
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 */
class LocatorRegistrationListener extends AbstractListener implements 
    ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $modules = array();

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * loadModule 
     *
     * Check each loaded module to see if it implements LocatorRegistered. If it 
     * does, we add it to an internal array for later.
     * 
     * @param  ModuleEvent $e 
     * @return void
     */
    public function loadModule(ModuleEvent $e)
    {
        if (!$e->getModule() instanceof LocatorRegisteredInterface) {
            return;
        }
        $this->modules[] = $e->getModule();
    }

    /**
     * loadModulesPost 
     *
     * Once all the modules are loaded, loop 
     * 
     * @param  Event $e 
     * @return void
     */
    public function loadModulesPost(Event $e)
    {
        $moduleManager = $e->getTarget();
        $events        = $moduleManager->events()->getSharedManager();

        // Shared instance for module manager
        $events->attach('application', 'bootstrap', function ($e) use ($moduleManager) {
            $moduleClassName = get_class($moduleManager);
            $application     = $e->getApplication();
            $services        = $application->getServiceManager();
            if (!$services->has($moduleClassName)) {
                $services->setService($moduleClassName, $moduleManager);
            }
        }, 1000);

        if (0 === count($this->modules)) {
            return;
        }

        // Attach to the bootstrap event if there are modules we need to process
        $events->attach('application', 'bootstrap', array($this, 'onBootstrap'), 1000);
    }

    /**
     * Bootstrap listener 
     *
     * This is ran during the MVC bootstrap event because it requires access to 
     * the DI container.
     *
     * @TODO: Check the application / locator / etc a bit better to make sure 
     * the env looks how we're expecting it to?
     * @param Event $e 
     * @return void
     */
    public function onBootstrap(Event $e)
    {
        $application = $e->getApplication();
        $services    = $application->getServiceManager();

        foreach ($this->modules as $module) {
            $moduleClassName = get_class($module);
            if (!$services->has($moduleClassName)) {
                $services->setService($moduleClassName, $module);
            }
        }
    }

    /**
     * Attach one or more listeners
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('loadModule', array($this, 'loadModule'), 1000);
        $this->listeners[] = $events->attach('loadModules.post', array($this, 'loadModulesPost'), 9000);
        return $this;
    }

    /**
     * Detach all previously attached listeners
     *
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
}
