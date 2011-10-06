<?php

namespace Zend\Mvc;

use Zend\EventManager\EventDescription as Event;

interface EventAware
{
    public function setEvent(Event $event);
    public function getEvent();
}
