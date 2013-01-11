<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc;

use ArrayObject;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;


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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'));
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
        $events           = $application->getEventManager();
        $controllerLoader = $application->getServiceManager()->get('ControllerLoader');

        try {
            $controller = $controllerLoader->get($controllerName);
        } catch (ServiceNotFoundException $exception) {
            $return = $this->marshallControllerNotFoundEvent($application::ERROR_CONTROLLER_NOT_FOUND, $controllerName, $exception, $e, $application);
            return $this->complete($return, $e);
        } catch (ServiceNotCreatedException $exception) {
            $return = $this->marshallControllerNotFoundEvent($application::ERROR_CONTROLLER_NOT_FOUND, $controllerName, $exception, $e, $application);
            return $this->complete($return, $e);
        } catch (Exception\InvalidControllerException $exception) {
            $return = $this->marshallControllerNotFoundEvent($application::ERROR_CONTROLLER_INVALID, $controllerName, $exception, $e, $application);
            return $this->complete($return, $e);
        } catch (\Exception $exception) {
            $return = $this->marshallBadControllerEvent($controllerName, $exception, $e, $application);
            return $this->complete($return, $e);
        }

        $request  = $e->getRequest();
        $response = $application->getResponse();

        if ($controller instanceof InjectApplicationEventInterface) {
            $controller->setEvent($e);
        }

        try {
            $return = $controller->dispatch($request, $response);
        } catch (\Exception $ex) {
            $e->setError($application::ERROR_EXCEPTION)
                  ->setController($controllerName)
                  ->setControllerClass(get_class($controller))
                  ->setParam('exception', $ex);
            $results = $events->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $e);
            $return = $results->last();
            if (! $return) {
                $return = $e->getResult();
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

    /**
     * Marshall a controller not found exception event
     *
     * @param  string $type
     * @param  string $controllerName
     * @param  \Exception $exception
     * @param  MvcEvent $event
     * @param  Application $application
     * @return mixed
     */
    protected function marshallControllerNotFoundEvent(
        $type,
        $controllerName,
        \Exception $exception,
        MvcEvent $event,
        Application $application
    ) {
        $event->setError($type)
              ->setController($controllerName)
              ->setControllerClass('invalid controller class or alias: ' . $controllerName)
              ->setParam('exception', $exception);

        $events  = $application->getEventManager();
        $results = $events->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
        $return  = $results->last();
        if (! $return) {
            $return = $event->getResult();
        }
        return $return;
    }

    /**
     * Marshall a bad controller exception event
     *
     * @param  string $controllerName
     * @param  \Exception $exception
     * @param  MvcEvent $event
     * @param  Application $application
     * @return mixed
     */
    protected function marshallBadControllerEvent(
        $controllerName,
        \Exception $exception,
        MvcEvent $event,
        Application $application
    ) {
        $event->setError($application::ERROR_EXCEPTION)
              ->setController($controllerName)
              ->setParam('exception', $exception);

        $events  = $application->getEventManager();
        $results = $events->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
        $return  = $results->last();
        if (! $return) {
            $return = $event->getResult();
        }

        return $return;
    }
}
