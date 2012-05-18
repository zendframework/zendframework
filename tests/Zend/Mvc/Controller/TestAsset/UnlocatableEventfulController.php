<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\EventManager\EventInterface as Event,
    Zend\Mvc\InjectApplicationEventInterface,
    Zend\Stdlib\DispatchableInterface,
    Zend\Stdlib\RequestInterface as Request,
    Zend\Stdlib\ResponseInterface as Response;

class UnlocatableEventfulController implements DispatchableInterface, InjectApplicationEventInterface
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
