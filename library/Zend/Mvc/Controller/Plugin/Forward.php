<?php

namespace Zend\Mvc\Controller\Plugin;

use Zend\Di\LocatorInterface,
    Zend\Mvc\InjectApplicationEventInterface,
    Zend\Mvc\Exception,
    Zend\Mvc\LocatorAwareInterface,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch,
    Zend\Stdlib\DispatchableInterface as Dispatchable;

class Forward extends AbstractPlugin
{
    protected $event;
    protected $locator;
    protected $maxNestedForwards = 10;
    protected $numNestedForwards = 0;

    public function setMaxNestedForwards($maxNestedForwards)
    {
        $this->maxNestedForwards = (int) $maxNestedForwards;
        return $this;
    }

    /**
     * Dispatch another controller
     * 
     * @param  string $name Controller name; either a class name or an alias used in the DI container or service locator
     * @param  null|array $params Parameters with which to seed a custom RouteMatch object for the new controller
     * @return mixed
     * @throws Exception\DomainException if composed controller does not define InjectApplicationEventInterface
     *         or Locator aware; or if the discovered controller is not dispatchable
     */
    public function dispatch($name, array $params = null)
    {
        $event   = $this->getEvent();
        $locator = $this->getLocator();

        $controller = $locator->get($name);
        if (!$controller instanceof Dispatchable) {
            throw new Exception\DomainException('Can only forward to DispatchableInterface classes; class of type ' . get_class($controller) . ' received');
        }
        if ($controller instanceof InjectApplicationEventInterface) {
            $controller->setEvent($event);
        }
        if ($controller instanceof LocatorAwareInterface) {
            $controller->setLocator($locator);
        }

        // Allow passing parameters to seed the RouteMatch with
        $cachedMatches = false;
        if ($params) {
            $matches       = new RouteMatch($params);
            $cachedMatches = $event->getRouteMatch();
            $event->setRouteMatch($matches);
        }

        if ($this->numNestedForwards > $this->maxNestedForwards) {
            throw new Exception\DomainException("Circular forwarding detected: greater than $this->maxNestedForwards nested forwards");
        }
        $this->numNestedForwards++;

        $return = $controller->dispatch($event->getRequest(), $event->getResponse());

        $this->numNestedForwards--;

        if ($cachedMatches) {
            $event->setRouteMatch($cachedMatches);
        }

        return $return;
    }

    /**
     * Get the locator
     * 
     * @return LocatorInterface
     * @throws Exception\DomainException if unable to find locator
     */
    protected function getLocator()
    {
        if ($this->locator) {
            return $this->locator;
        }

        $controller = $this->getController();

        if (!$controller instanceof LocatorAwareInterface) {
            throw new Exception\DomainException('Forward plugin requires controller implements LocatorAwareInterface');
        }
        $locator = $controller->getLocator();
        if (!$locator instanceof LocatorInterface) {
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
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException('Redirect plugin requires a controller that implements InjectApplicationEventInterface');
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
