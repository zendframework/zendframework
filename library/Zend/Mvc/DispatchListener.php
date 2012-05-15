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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc;

use ArrayObject;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Exception\ExceptionInterface as InstanceException;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\DispatchableInterface;

/**
 * Default dispatch listener
 *
 * Pulls controllers from the service manager's "ControllerLoader" service.
 * If the controller cannot be found, or is not dispatchable, sets up a "404" 
 * result. 
 *
 * If the controller subscribes to InjectApplicationEventInterface, it injects
 * the current MvcEvent into the controller.
 *
 * It then calls the controller's "dispatch" method, passing it the request and
 * response. If an exception occurs, it triggers the "dispatch.error" event,
 * in an attempt to return a 500 status.
 *
 * The return value of dispatching the controller is placed into the result
 * property of the MvcEvent, and returned.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DispatchListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach listeners to an event manager
     *
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('dispatch', array($this, 'onDispatch'));
    }

    /**
     * Detach listeners from an event manager
     * 
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Listen to the "dispatch" event
     * 
     * @param  MvcEvent $e 
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch       = $e->getRouteMatch();
        $controllerName   = $routeMatch->getParam('controller', 'not-found');
        $application      = $e->getApplication();
        $events           = $application->events();
        $controllerLoader = $application->getServiceManager()->get('ControllerLoader');

        $wasLoaded = false;
        $exception = false;
        try {
            $controller = $controllerLoader->get($controllerName);
            $wasLoaded  = true;
        } catch (\Exception $exception) {
            $wasLoaded =false;
        }

        if (!$wasLoaded) {
            $error = clone $e;
            $error->setError($application::ERROR_CONTROLLER_NOT_FOUND)
                  ->setController($controllerName)
                  ->setParam('exception', $exception);

            $results = $events->trigger('dispatch.error', $error);
            if (count($results)) {
                $return = $results->last();
            } else {
                $return = $error->getParams();
            }
            return $this->complete($return, $e);
        }

        if (!$controller instanceof DispatchableInterface) {
            $error = clone $e;
            $error->setError($application::ERROR_CONTROLLER_INVALID)
                ->setController($controllerName)
                ->setControllerClass(get_class($controller));

            $results = $events->trigger('dispatch.error', $error);
            if (count($results)) {
                $return  = $results->last();
            } else {
                $return = $error->getParams();
            }
            return $this->complete($return, $e);
        }

        $request  = $e->getRequest();
        $response = $application->getResponse();

        if ($controller instanceof InjectApplicationEventInterface) {
            $controller->setEvent($e);
        }

        try {
            $return   = $controller->dispatch($request, $response);
        } catch (\Exception $ex) {
            $error = clone $e;
            $error->setError($application::ERROR_EXCEPTION)
                  ->setController($controllerName)
                  ->setControllerClass(get_class($controller))
                  ->setParam('exception', $ex);
            $results = $events->trigger('dispatch.error', $error);
            if (count($results)) {
                $return  = $results->last();
            } else {
                $return = $error->getParams();
            }
        }

        return $this->complete($return, $e);
    }

    /**
     * Complete the dispatch
     * 
     * @param  mixed $return 
     * @param  MvcEvent $event 
     * @return mixed
     */
    protected function complete($return, MvcEvent $event)
    {
        if (!is_object($return)) {
            if (ArrayUtils::hasStringKeys($return)) {
                $return = new ArrayObject($return, ArrayObject::ARRAY_AS_PROPS);
            }
        }
        $event->setResult($return);
        return $return;
    }
}
