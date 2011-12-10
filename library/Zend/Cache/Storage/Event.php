<?php

namespace Zend\Cache\Storage;
use Zend\EventManager\Event as BaseEvent,
    ArrayObject;

class Event extends BaseEvent
{

    /**
     * Constructor
     *
     * Accept a storage adapter and its parameters.
     *
     * @param  string $name Event name
     * @param  Zend\Cache\Storage\Adapter $storage
     * @param  ArrayObject $params
     * @param  mixed $result
     * @return void
     */
    public function __construct($name, Adapter $storage, ArrayObject $params)
    {
        parent::__construct($name, $storage, $params);
    }

    /**
     * Set the event target/context
     *
     * @param Adapter $target
     * @return Event
     * @see Zend\EventManager\Event::setTarget()
     */
    public function setTarget($target)
    {
        return $this->setStorage($target);
    }

    /**
     * Alias of setTarget
     *
     * @param Adapter $adapter
     * @return Event
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
     * @return Adapter
     */
    public function getStorage()
    {
        return $this->getTarget();
    }

}

