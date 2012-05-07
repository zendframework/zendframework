<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\EventManager\EventDescription as Event,
    Zend\Mvc\InjectApplicationEventInterface,
    Zend\Stdlib\Dispatchable,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response;

class UnlocatableEventfulController implements Dispatchable, InjectApplicationEventInterface
{
    protected $event;

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function dispatch(Request $request, Response $response = null)
    {
    }
}
