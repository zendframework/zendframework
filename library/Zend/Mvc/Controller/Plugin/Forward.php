<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Forward extends AbstractPlugin
{
    protected $event;
    protected $locator;
    protected $maxNestedForwards = 10;
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
        $event   = $this->getEvent();
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
        if (!$scoped) {
            if ($controller instanceof InjectApplicationEventInterface) {
                $controller->setEvent($event);
            }
            if ($controller instanceof ServiceLocatorAwareInterface) {
                $controller->setServiceLocator($locator);
            }
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
