<?php

namespace ZendTest\Cache\Storage\TestAsset;

use Zend\Cache\Storage\Plugin,
    Zend\Cache\Storage\Plugin\AbstractPlugin,
    Zend\EventManager\EventCollection,
    Zend\EventManager\Event;

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

    public function attach(EventCollection $eventCollection)
    {
        $handles = array();
        foreach ($this->eventCallbacks as $eventName => $method) {
            $handles[] = $eventCollection->attach($eventName, array($this, $method));
        }
        $this->handles[ \spl_object_hash($eventCollection) ] = $handles;
    }

    public function detach(EventCollection $eventCollection)
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
