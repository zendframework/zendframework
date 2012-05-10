<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Module
 */
namespace Zend\Module\Listener;

use Zend\EventManager\EventManagerInterface,
    Zend\EventManager\ListenerAggregateInterface,
    Zend\Loader\ModuleAutoloader;

/**
 * Default listener aggregate
 * 
 * @category   Zend
 * @package    Zend_Module
 * @subpackage Listener
 */
class DefaultListenerAggregate extends AbstractListener implements ListenerAggregateInterface
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
     * @param EventManagerInterface $events
     * @return DefaultListenerAggregate
     */
    public function attach(EventManagerInterface $events)
    {
        $options = $this->getOptions();
        $configListener = $this->getConfigListener();
        $locatorRegistrationListener = new LocatorRegistrationListener($options);
        $moduleAutoloader = new ModuleAutoloader($options->getModulePaths());

        $this->listeners[] = $events->attach('loadModules.pre', array($moduleAutoloader, 'register'), 1000);
        $this->listeners[] = $events->attach('loadModule.resolve', new ModuleResolverListener, 1000);
        $this->listeners[] = $events->attach('loadModule', new AutoloaderListener($options), 2000);
        $this->listeners[] = $events->attach('loadModule', new InitTrigger($options), 1000);
        $this->listeners[] = $events->attach('loadModule', new OnBootstrapListener($options), 1000);
        $this->listeners[] = $events->attachAggregate($locatorRegistrationListener);
        $this->listeners[] = $events->attachAggregate($configListener);

        return $this;
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     * @return DefaultListenerAggregate
     */
    public function detach(EventManagerInterface $events)
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
