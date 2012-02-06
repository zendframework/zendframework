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
        $im              = $e->getParam('application')->getLocator()->instanceManager();
        $moduleInstance  = $this->moduleEvent->getModule();
        $moduleClassName = get_class($moduleInstance);

        if (!$im->hasTypePreferences($moduleClassName)) {
            $im->addTypePreference($moduleClassName, $moduleInstance);
        }
    }
}
