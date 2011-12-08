<?php

namespace Zend\Module\Listener;

use Zend\EventManager\ListenerAggregate,
    Zend\EventManager\EventCollection;

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
        $this->listeners[] = $events->attach('loadModule.resolve', new ModuleResolverListener, 1000);
        $this->listeners[] = $events->attach('loadModule', new AutoloaderListener($options), 2000);
        $this->listeners[] = $events->attach('loadModule', new InitTrigger($options), 1000);
        $this->listeners[] = $events->attach('loadModule', $configListener, 1000);
        $this->listeners[] = $events->attach('loadModules.post', array($configListener, 'mergeConfigGlobPaths'), 1000);
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
            $events->detach($handler);
            unset($this->listeners[$key]);
        }
        $this->handlers = array();
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
