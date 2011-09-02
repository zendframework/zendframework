<?php

namespace Zf2Mvc;

use Zend\EventManager\EventCollection;

interface AppContext
{
    public function setConfiguration($config);
    public function setDispatcher(Dispatcher $dispatcher);
    public function setEventManager(EventCollection $events);
    public function setLocator($locator);
    public function setRouter(Router $router);

    public function getConfiguration();
    public function getDispatcher();
    public function getLocator();
    public function getRouter();

    public function events();

    public function run();
}
