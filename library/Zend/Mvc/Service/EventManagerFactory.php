<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\EventManager\SharedEventManager,
    Zend\EventManager\EventManager;

class EventManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        static $sharedEventManager = null;
        if (!$sharedEventManager) {
            $sharedEventManager = new SharedEventManager();
        }
        $em = new EventManager();
        $em->setSharedManager($sharedEventManager);
        return $em;
    }
}