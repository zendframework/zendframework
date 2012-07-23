<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\Mvc\Exception;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 */
class Forward extends AbstractPlugin
{
    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var ServiceLocatorInterface
     */
    protected $locator;

    /**
     * @var int
     */
    protected $maxNestedForwards = 10;

    /**
     * @var int
     */
    protected $numNestedForwards = 0;

    /**
     * Set maximum number of nested forwards allowed
     *
     * @param  int $maxNestedForwards
     * @return Forward
     */
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
        $event   = clone($this->getEvent());
        $locator = $this->getLocator();
        $scoped  = false;

        // Use the controller loader when possible
        if ($locator->has('ControllerLoader')) {
            $locator = $locator->get('ControllerLoader');
            $scoped  = true;
        }

        $controller = $locator->get($name);
        if (!$controller instanceof Dispatchable) {
            throw new Exception\DomainException('Can only forward to DispatchableInterface classes; class of type ' . get_class($controller) . ' received');
        }
        if ($controller instanceof InjectApplicationEventInterface) {
            $controller->setEvent($event);
        }
        if (!$scoped) {
            if ($controller instanceof ServiceLocatorAwareInterface) {
                $controller->setServiceLocator($locator);
            }
        }

        // Allow passing parameters to seed the RouteMatch with
        if ($params) {
            $event->setRouteMatch(new RouteMatch($params));
        }

        if ($this->numNestedForwards > $this->maxNestedForwards) {
            throw new Exception\DomainException("Circular forwarding detected: greater than $this->maxNestedForwards nested forwards");
        }
        $this->numNestedForwards++;

        $return = $controller->dispatch($event->getRequest(), $event->getResponse());

        $this->numNestedForwards--;

        return $return;
    }

    /**
     * Get the locator
     *
     * @return ServiceLocatorInterface
     * @throws Exception\DomainException if unable to find locator
     */
    protected function getLocator()
    {
        if ($this->locator) {
            return $this->locator;
        }

        $controller = $this->getController();

        if (!$controller instanceof ServiceLocatorAwareInterface) {
            throw new Exception\DomainException('Forward plugin requires controller implements ServiceLocatorAwareInterface');
        }
        $locator = $controller->getServiceLocator();
        if (!$locator instanceof ServiceLocatorInterface) {
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
