<?php

namespace Zf2Mvc;

use PHPUnit_Framework_TestCase as TestCase;

class ApplicationTest extends TestCase
{
    public function testEventManagerIsLazyLoaded()
    {
        $app = new Application();
        $events = $app->events();
        $this->assertInstanceOf('Zend\EventManager\EventCollection', $events);
        $this->assertInstanceOf('Zend\EventManager\EventManager', $events);
    }
}
