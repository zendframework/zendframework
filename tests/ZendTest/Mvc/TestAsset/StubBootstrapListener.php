<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\TestAsset;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class StubBootstrapListener implements ListenerAggregateInterface
{
    protected $listeners = array();

    /**
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach (EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'onBootstrap'));
    }

    /**
     * @see \Zend\EventManager\ListenerAggregateInterface::detach()
     */
    public function detach (EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function getListeners()
    {
        return $this->listeners;
    }

    public function onBootstrap($e)
    {
    }
}
