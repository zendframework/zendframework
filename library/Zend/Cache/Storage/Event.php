<?php

namespace Zend\Cache\Storage;
use Zend\EventManager\Event as BaseEvent;

class Event extends BaseEvent
{

    /**
     * Set the event target/context
     *
     * @param Zend\Cache\Storage\Adapter $target
     * @return Zend\Cache\Storage\Event
     * @see Zend\EventManager\Event::setTarget()
     */
    public function setTarget($target)
    {
        return $this->setStorage($target);
    }

    /**
     * Alias of setTarget
     *
     * @param Zend\Cache\Storage\Adapter $adapter
     * @return Zend\Cache\Storage\Event
     * @see Zend\EventManager\Event::setTarget()
     */
    public function setStorage(Adapter $adapter)
    {
        $this->target = $adapter;
        return $this;
    }

    /**
     * Alias of getTarget
     *
     * @return Zend\Cache\Storage\Adapter
     */
    public function getStorage()
    {
        return $this->getTarget();
    }

}

