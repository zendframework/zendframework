<?php

namespace Zend\Module\Listener;

use Zend\EventManager\ListenerAggregate,
    Zend\EventManager\EventCollection,
    Zend\Loader\ModuleAutoloader;

class DefaultListenerAggregate extends AbstractListener
    implements ListenerAggregate
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var ConfigMerger
     */
    protected $configListener;

    /**
     * Attach one or more listeners
     *
     * @param EventCollection $events
     * @return void
     */
    public function attach(EventCollection $events)
    {
        $options = $this->getOptions();
        $configListener = $this->getConfigListener();
        $locatorRegistrationListener = new LocatorRegistrationListener($options);
        $moduleAutoloader = new ModuleAutoloader($options->getModulePaths());

        $this->listeners[] = $events->attach('loadModules.pre', array($moduleAutoloader, 'register'), 1000);
        $this->listeners[] = $events->attach('loadModule.resolve', new ModuleResolverListener, 1000);
        $this->listeners[] = $events->attach('loadModule', new AutoloaderListener($options), 2000);
        $this->listeners[] = $events->attach('loadModule', new InitTrigger($options), 1000);
        $this->listeners[] = $events->attachAggregate($locatorRegistrationListener);
        $this->listeners[] = $events->attachAggregate($configListener);

        return $this;
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventCollection $events
     * @return void
     */
    public function detach(EventCollection $events)
    {
        foreach ($this->listeners as $key => $listener) {
            if ($listener instanceof ListenerAggregate) {
                $listener->detach($events);
            } else {
                $events->detach($listener);
            }
            unset($this->listeners[$key]);
        }
        $this->listeners = array();
        return $this;
    }

    /**
     * Get the config merger.
     *
     * @return ConfigMerger
     */
    public function getConfigListener()
    {
        if (!$this->configListener instanceof ConfigMerger) {
            $this->setConfigListener(new ConfigListener($this->getOptions()));
        }
        return $this->configListener;
    }

    /**
     * Set the config merger to use.
     *
     * @param ConfigMerger $configListener
     * @return DefaultListenerAggregate
     */
    public function setConfigListener(ConfigMerger $configListener)
    {
        $this->configListener = $configListener;
        return $this;
    }
}
