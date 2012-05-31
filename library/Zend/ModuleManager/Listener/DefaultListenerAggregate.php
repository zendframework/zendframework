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

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Loader\ModuleAutoloader;
use Zend\Stdlib\CallbackHandler;

/**
 * Default listener aggregate
 * 
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 */
class DefaultListenerAggregate extends AbstractListener implements 
    ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var ConfigMergerInterface
     */
    protected $configListener;

    /**
     * Attach one or more listeners
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $options                     = $this->getOptions();
        $configListener              = $this->getConfigListener();
        $locatorRegistrationListener = new LocatorRegistrationListener($options);
        $moduleAutoloader            = new ModuleAutoloader($options->getModulePaths());

        $this->listeners[] = $events->attach('loadModules.pre', array($moduleAutoloader, 'register'), 1000);
        $this->listeners[] = $events->attach('loadModule.resolve', new ModuleResolverListener, 1000);
        $this->listeners[] = $events->attach('loadModule', new AutoloaderListener($options), 2000);
        $this->listeners[] = $events->attach('loadModule', new InitTrigger($options), 1000);
        $this->listeners[] = $events->attach('loadModule', new OnBootstrapListener($options), 1000);
        $this->listeners[] = $events->attach($locatorRegistrationListener);
        $this->listeners[] = $events->attach($configListener);
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
            $detached = false;
            if ($listener === $this) {
                continue;
            }
            if ($listener instanceof ListenerAggregateInterface) {
                $detached = $listener->detach($events);
            } elseif ($listener instanceof CallbackHandler) {
                $detached = $events->detach($listener);
            }

            if ($detached) {
                unset($this->listeners[$key]);
            }
        }
    }

    /**
     * Get the config merger.
     *
     * @return ConfigMergerInterface
     */
    public function getConfigListener()
    {
        if (!$this->configListener instanceof ConfigMergerInterface) {
            $this->setConfigListener(new ConfigListener($this->getOptions()));
        }
        return $this->configListener;
    }

    /**
     * Set the config merger to use.
     *
     * @param  ConfigMergerInterface $configListener
     * @return DefaultListenerAggregate
     */
    public function setConfigListener(ConfigMergerInterface $configListener)
    {
        $this->configListener = $configListener;
        return $this;
    }
}
