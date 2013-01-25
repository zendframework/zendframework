<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\TestAsset;

use Zend\Cache\Storage\Plugin;
use Zend\Cache\Storage\Plugin\AbstractPlugin;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\Event;

class MockPlugin extends AbstractPlugin
{

    protected $options;
    protected $handles = array();
    protected $calledEvents = array();
    protected $eventCallbacks  = array(
        'setItem.pre'  => 'onSetItemPre',
        'setItem.post' => 'onSetItemPost'
    );

    public function __construct($options = array())
    {
        if (is_array($options)) {
            $options = new Plugin\PluginOptions($options);
        }
        if ($options instanceof Plugin\PluginOptions) {
            $this->setOptions($options);
        }
    }

    public function setOptions(Plugin\PluginOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function attach(EventManagerInterface $eventCollection)
    {
        $handles = array();
        foreach ($this->eventCallbacks as $eventName => $method) {
            $handles[] = $eventCollection->attach($eventName, array($this, $method));
        }
        $this->handles[ \spl_object_hash($eventCollection) ] = $handles;
    }

    public function detach(EventManagerInterface $eventCollection)
    {
        $index = \spl_object_hash($eventCollection);
        foreach ($this->handles[$index] as $i => $handle) {
            $eventCollection->detach($handle);
            unset($this->handles[$index][$i]);
        }

        // remove empty handles of event collection
        if (!$this->handles[$index]) {
            unset($this->handles[$index]);
        }
    }

    public function onSetItemPre(Event $event)
    {
        $this->calledEvents[] = $event;
    }

    public function onSetItemPost(Event $event)
    {
        $this->calledEvents[] = $event;
    }

    public function getHandles()
    {
        return $this->handles;
    }

    public function getEventCallbacks()
    {
        return $this->eventCallbacks;
    }

    public function getCalledEvents()
    {
        return $this->calledEvents;
    }
}
