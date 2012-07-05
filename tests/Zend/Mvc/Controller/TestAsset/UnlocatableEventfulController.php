<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

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
