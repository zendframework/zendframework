<?php

namespace Zend\Mvc\Controller\Plugin;

use Zend\Di\Locator,
    Zend\Mvc\InjectApplicationEvent,
    Zend\Mvc\Exception,
    Zend\Mvc\LocatorAware,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch,
    Zend\Stdlib\Dispatchable;

class Forward extends AbstractPlugin
{
    protected $event;
    protected $locator;

    /**
     * Dispatch another controller
     * 
     * @param  string $name Controller name; either a class name or an alias used in the DI container or service locator
     * @param  null|array $params Parameters with which to seed a custom RouteMatch object for the new controller
     * @return mixed
     * @throws Exception\DomainException if composed controller does not define InjectApplicationEvent
     *         or Locator aware; or if the discovered controller is not dispatchable
     */
    public function dispatch($name, array $params = null)
    {
        $event   = $this->getEvent();
        $locator = $this->getLocator();

        $controller = $locator->get($name);
        if (!$controller instanceof Dispatchable) {
            throw new Exception\DomainException('Can only forward to Dispatchable classes; class of type ' . get_class($controller) . ' received');
        }
        if ($controller instanceof InjectApplicationEvent) {
            $controller->setEvent($event);
        }
        if ($controller instanceof LocatorAware) {
            $controller->setLocator($locator);
        }

        // Allow passing parameters to seed the RouteMatch with
        $cachedMatches = false;
        if ($params) {
            $matches       = new RouteMatch($params);
            $cachedMatches = $event->getRouteMatch();
            $event->setRouteMatch($matches);
        }

        $return = $controller->dispatch($event->getRequest(), $event->getResponse());

        if ($cachedMatches) {
            $event->setRouteMatch($cachedMatches);
        }

        return $return;
    }

    /**
     * Get the locator
     * 
     * @return Locator
     * @throws Exception\DomainException if unable to find locator
     */
    protected function getLocator()
    {
        if ($this->locator) {
            return $this->locator;
        }

        $controller = $this->getController();

        if (!$controller instanceof LocatorAware) {
            throw new Exception\DomainException('Forward plugin requires controller implements LocatorAware');
        }
        $locator = $controller->getLocator();
        if (!$locator instanceof Locator) {
            throw new Exception\DomainException('Forward plugin requires controller composes Locator');
        }
        $this->locator = $locator;
        return $this->locator;
    }

    /**
     * Get the event
     * 
     * @return MvcEvent
     * @throws Exception\DomainException if unable to find event
     */
    protected function getEvent()
    {
        if ($this->event) {
            return $this->event;
        }

        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEvent) {
            throw new Exception\DomainException('Redirect plugin requires a controller that implements InjectApplicationEvent');
        }

        $event = $controller->getEvent();
        if (!$event instanceof MvcEvent) {
            $params = array();
            if ($event) {
                $params = $event->getParams();
            }
            $event  = new MvcEvent();
            $event->setParams($params);
        }
        $this->event = $event;

        return $this->event;
    }
}
