<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ModuleManager\Listener;

use Zend\Loader\ModuleAutoloader;
use Zend\ModuleManager\ModuleEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Module loader listener
 */
class ModuleLoaderListener extends AbstractListener implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $moduleLoader;

    /**
     * @var bool
     */
    protected $generateCache;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * Constructor.
     *
     * Creates an instance of the ModuleAutoloader and injects the module paths
     * into it.
     *
     * @param  ListenerOptions $options
     */
    public function __construct(ListenerOptions $options = null)
    {
        parent::__construct($options);

        $this->generateCache = $this->options->getModuleMapCacheEnabled();
        $this->moduleLoader  = new ModuleAutoloader($this->options->getModulePaths());

        if ($this->hasCachedClassMap()) {
            $this->generateCache = false;
            $this->moduleLoader->setModuleClassMap($this->getCachedConfig());
        }
    }

    /**
     * Attach one or more listeners
     *
     * @param  EventManagerInterface $events
     * @return LocatorRegistrationListener
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            ModuleEvent::EVENT_LOAD_MODULES,
            array($this->moduleLoader, 'register'),
            9000
        );

        if ($this->generateCache) {
            $this->listeners[] = $events->attach(
                ModuleEvent::EVENT_LOAD_MODULES_POST,
                array($this, 'onLoadModulesPost')
            );
        }

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

    /**
     * @return bool
     */
    protected function hasCachedClassMap()
    {
        if (
            $this->options->getModuleMapCacheEnabled()
            && file_exists($this->options->getModuleMapCacheFile())
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getCachedConfig()
    {
        return include $this->options->getModuleMapCacheFile();
    }

    /**
     * loadModulesPost
     *
     * Unregisters the ModuleLoader and generates the module class map cache.
     *
     * @param  ModuleEvent $e
     */
    public function onLoadModulesPost(ModuleEvent $event)
    {
        $this->moduleLoader->unregister();
        $this->writeArrayToFile(
            $this->options->getModuleMapCacheFile(),
            $this->moduleLoader->getModuleClassMap()
        );
    }
}
