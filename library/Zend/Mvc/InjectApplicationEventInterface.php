<?php

namespace Zend\Mvc;

use Zend\EventManager\EventInterface as Event;

interface InjectApplicationEventInterface
{
    public function setEvent(Event $event);
    public function getEvent();
}
