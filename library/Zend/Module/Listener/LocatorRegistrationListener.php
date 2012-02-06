<?php

namespace Zend\Module\Listener;

use Zend\EventManager\StaticEventManager,
    Zend\Module\ModuleEvent,
    Zend\EventManager\Event;

class LocatorRegistrationListener extends AbstractListener
{
    protected $moduleEvent;

    public function __invoke(ModuleEvent $e)
    {
        $this->moduleEvent = $e;
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'addTypePreference'), 1000);
    }

    public function addTypePreference(Event $e)
    {
        $moduleInstance = $this->moduleEvent->getModule();
        $moduleClassName = get_class($moduleInstance);

        $di = $e->getParam('application')->getLocator();
        $im = $di->instanceManager();
        if ($im->hasTypePreferences($moduleClassName)) return;
        $im->addTypePreference($moduleClassName, $moduleInstance);
    }
}
