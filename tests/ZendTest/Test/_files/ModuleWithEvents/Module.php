<?php

namespace ModuleWithEvents;

use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap($e)
    {
        $application = $e->getApplication();
        $events      = $application->getEventManager();
        $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -1000);
    }

    public function onRoute($e)
    {
        $routeMatch = $e->getRouteMatch();
        if ($routeMatch->getMatchedRouteName() == "myroutebis") {
            return;
        }

        $application = $e->getApplication();
        $events      = $application->getEventManager()->getSharedManager();
        $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_FINISH, function($e) use ($application) {
            $response = $application->getResponse();
            $response->setContent("<html></html>");
        }, 1000000);
    }
}
