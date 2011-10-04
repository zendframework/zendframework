<?php

namespace Zend\Mvc\Controller\Plugin;

use Zend\Di\Locator,
    Zend\Mvc\EventAware,
    Zend\Mvc\Exception,
    Zend\Mvc\LocatorAware,
    Zend\Mvc\MvcEvent,
    Zend\Stdlib\Dispatchable;

class Forward extends AbstractPlugin
{
    protected $event;
    protected $locator;

    /**
     * Dispatch another controller
     * 
     * @param  string $name Controller name; either a class name or an alias used in the DI container or service locator
     * @return mixed
     * @throws Exception\DomainException if composed controller is not EventAware 
     *         or Locator aware; or if the discovered controller is not dispatchable
     */
    public function dispatch($name)
    {
        $event   = $this->getEvent();
        $locator = $this->getLocator();

        $controller = $locator->get($name);
        if (!$controller instanceof Dispatchable) {
            throw new Exception\DomainException('Can only forward to Dispatchable classes; class of type ' . get_class($controller) . ' received');
        }
        if ($controller instanceof EventAware) {
            $controller->setEvent($event);
        }
        if ($controller instanceof LocatorAware) {
            $controller->setLocator($locator);
        }

        return $controller->dispatch($event->getRequest(), $event->getResponse());
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
        if (!$controller instanceof EventAware) {
            throw new Exception\DomainException('Redirect plugin requires a controller that is EventAware');
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
