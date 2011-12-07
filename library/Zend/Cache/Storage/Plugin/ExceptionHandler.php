<?php

namespace Zend\Cache\Storage\Plugin;
use Zend\Cache\Storage\Plugin,
    Zend\Cache\Storage\ExceptionEvent,
    Zend\EventManager\EventCollection,
    Zend\Cache\Exception\InvalidArgumentException;

class ExceptionHandler implements Plugin
{

    protected $_callback = null;
    protected $_throwExceptions  = true;

    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    public function setOptions($options)
    {
        foreach ($options as $name => $value) {
            $m = 'set' . str_replace('_', '', $name);
            $this->$m($value);
        }
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['callback']         = $this->getExceptionHandler();
        $options['throw_exceptions'] = $this->getThrowExceptions();
        return $options;
    }

    public function setCallback($callback)
    {
        if (!is_callable($callback, true)) {
            throw new InvalidArgumentException('Not a valid callback');
        }
        $this->_callback = $callback;
    }

    public function getCallback()
    {
        return $this->_callback;
    }

    public function setThrowExceptions($flag)
    {
        $this->_throwExceptions = (bool)$flag;
    }

    public function getThrowExceptions()
    {
        return $this->_throwExceptions;
    }

    public function attach(EventCollection $eventCollection)
    {
        $index = \spl_object_hash($eventCollection);
        if (isset($this->handles[$index])) {
            throw new LogicException('Plugin already attached');
        }

        $callback = array($this, 'onException');
        $handles  = array();
        $this->handles[$index] = & $handles;

        // read
        $handles[] = $eventCollection->attach('getItem.exception', $callback);
        $handles[] = $eventCollection->attach('getItems.exception', $callback);

        $handles[] = $eventCollection->attach('hasItem.exception', $callback);
        $handles[] = $eventCollection->attach('hasItems.exception', $callback);

        $handles[] = $eventCollection->attach('getMetadata.exception', $callback);
        $handles[] = $eventCollection->attach('getMetadatas.exception', $callback);

        // non-blocking
        $handles[] = $eventCollection->attach('getDelayed.exception', $callback);
        $handles[] = $eventCollection->attach('find.exception', $callback);

        $handles[] = $eventCollection->attach('fetch.exception', $callback);
        $handles[] = $eventCollection->attach('fetchAll.exception', $callback);

        // write
        $handles[] = $eventCollection->attach('setItem.exception', $callback);
        $handles[] = $eventCollection->attach('setItems.exception', $callback);

        $handles[] = $eventCollection->attach('addItem.exception', $callback);
        $handles[] = $eventCollection->attach('addItems.exception', $callback);

        $handles[] = $eventCollection->attach('replaceItem.exception', $callback);
        $handles[] = $eventCollection->attach('replaceItems.exception', $callback);

        $handles[] = $eventCollection->attach('touchItem.exception', $callback);
        $handles[] = $eventCollection->attach('touchItems.exception', $callback);

        $handles[] = $eventCollection->attach('removeItem.exception', $callback);
        $handles[] = $eventCollection->attach('removeItems.exception', $callback);

        $handles[] = $eventCollection->attach('checkAndSetItem.exception', $callback);

        // increment / decrement item(s)
        $handles[] = $eventCollection->attach('incrementItem.exception', $callback);
        $handles[] = $eventCollection->attach('incrementItems.exception', $callback);

        $handles[] = $eventCollection->attach('decrementItem.exception', $callback);
        $handles[] = $eventCollection->attach('decrementItems.exception', $callback);

        // clear
        $handles[] = $eventCollection->attach('clear.exception', $callback);
        $handles[] = $eventCollection->attach('clearByNamespace.exception', $callback);

        // additional
        $handles[] = $eventCollection->attach('optimize.exception', $callback);
        $handles[] = $eventCollection->attach('getCapacity.exception', $callback);

        return $this;
    }

    public function detach(EventCollection $eventCollection)
    {
        $index = \spl_object_hash($eventCollection);
        if (!isset($this->handles[$index])) {
            throw new LogicException('Plugin not attached');
        }

        // detach all handles of this index
        foreach ($this->handles[$index] as $handle) {
            $eventCollection->detach($handle);
        }

        // remove all detached handles
        unset($this->handles[$index]);

        return $this;
    }

    public function onException(ExceptionEvent $event)
    {
        if ( ($callback = $this->getCallback()) ) {
            $callback($event->getException());
        }

        $event->setThrowException( $this->getThrowExceptions() );
    }

}
