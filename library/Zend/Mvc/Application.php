<?php

namespace Zend\Mvc;

use ArrayObject,
    Zend\Di\Exception\ClassNotFoundException,
    Zend\Di\Locator,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Http\Header\Cookie,
    Zend\Http\PhpEnvironment\Request as PhpHttpRequest,
    Zend\Http\PhpEnvironment\Response as PhpHttpResponse,
    Zend\Uri\Http as HttpUri,
    Zend\Stdlib\Dispatchable,
    Zend\Stdlib\IsAssocArray,
    Zend\Stdlib\Parameters,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response;

/**
 * Main application class for invoking applications
 *
 * Expects the user will provide a Service Locator or Dependency Injector, as
 * well as a configured router. Once done, calling run() will invoke the
 * application, first routing, then dispatching the discovered controller. A
 * response will then be returned, which may then be sent to the caller.
 */
class Application implements AppContext
{
    const ERROR_CONTROLLER_CANNOT_DISPATCH = 'error-controller-cannot-dispatch';
    const ERROR_CONTROLLER_NOT_FOUND       = 'error-controller-not-found';
    const ERROR_CONTROLLER_INVALID         = 'error-controller-invalid';
    const ERROR_EXCEPTION                  = 'error-exception';
    const ERROR_ROUTER_NO_MATCH            = 'error-router-no-match';

    protected $event;
    protected $events;
    protected $locator;
    protected $request;
    protected $response;
    protected $router;

    /**
     * Set the event manager instance used by this context
     *
     * @param  EventCollection $events
     * @return Application
     */
    public function setEventManager(EventCollection $events)
    {
        $this->events = $events;
        return $this;
    }

    /**
     * Set a service locator/DI object
     *
     * @param  Locator $locator
     * @return Application
     */
    public function setLocator(Locator $locator)
    {
        $this->locator = $locator;
        return $this;
    }

    /**
     * Set the request object
     *
     * @param  Request $request
     * @return Application
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Set the response object
     *
     * @param  Response $response
     * @return Application
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Set the router used to decompose the request
     *
     * A router should return a metadata object containing a controller key.
     *
     * @param  Router\RouteStack $router
     * @return Application
     */
    public function setRouter(Router\RouteStack $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Set the MVC event instance
     * 
     * @param  MvcEvent $event 
     * @return Application
     */
    public function setMvcEvent(MvcEvent $event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Get the locator object
     *
     * @return null|Locator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Get the request object
     *
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->request instanceof Request) {
            $this->setRequest(new PhpHttpRequest);
        }
        return $this->request;
    }

    /**
     * Get the response object
     *
     * @return Response
     */
    public function getResponse()
    {
        if (!$this->response instanceof Response) {
            $this->setResponse(new PhpHttpResponse());
        }
        return $this->response;
    }

    /**
     * Get the router object
     *
     * @return Router
     */
    public function getRouter()
    {
        if (!$this->router instanceof Router\RouteStack) {
            $this->setRouter(new Router\SimpleRouteStack());
        }
        return $this->router;
    }

    /**
     * Get the MVC event instance
     * 
     * @return MvcEvent
     */
    public function getMvcEvent()
    {
        if ($this->event) {
            return $this->event;
        }

        $this->event = $event  = new MvcEvent();
        $event->setTarget($this);
        $event->setRequest($this->getRequest())
              ->setResponse($this->getResponse())
              ->setRouter($this->getRouter());
        return $event;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventCollection
     */
    public function events()
    {
        if (!$this->events instanceof EventCollection) {
            $this->setEventManager(new EventManager(array(__CLASS__, get_class($this))));
            $this->attachDefaultListeners();
        }
        return $this->events;
    }

    /**
     * Run the application
     *
     * @triggers route(MvcEvent)
     *           Routes the request, and sets the RouteMatch object in the event.
     * @triggers dispatch(MvcEvent)
     *           Dispatches a request, using the discovered RouteMatch and
     *           provided request.
     * @triggers dispatch.error(MvcEvent)
     *           On errors (controller not found, action not supported, etc.),
     *           populates the event with information about the error type,
     *           discovered controller, and controller class (if known).
     *           Typically, a handler should return a populated Response object
     *           that can be returned immediately.
     * @return SendableResponse
     */
    public function run()
    {
        $events = $this->events();
        $event  = $this->getMvcEvent();

        // Define callback used to determine whether or not to short-circuit
        $shortCircuit = function ($r) use ($event) {
            if ($r instanceof Response) {
                return true;
            }
            if ($event->getError()) {
                return true;
            }
            return false;
        };
        
        // Trigger route event
        $result = $events->trigger('route', $event, $shortCircuit);
        if ($result->stopped()) {
            $response = $result->last();
            if ($response instanceof Response) {
                $events->trigger('finish', $event);
                return $response;
            }
            if ($event->getError()) {
                return $this->completeRequest($event);
            }
            return $event->getResponse();
        }
        if ($event->getError()) {
            return $this->completeRequest($event);
        }

        // Trigger dispatch event
        $result = $events->trigger('dispatch', $event, $shortCircuit);

        // Complete response
        $response = $result->last();
        if ($response instanceof Response) {
            $events->trigger('finish', $event);
            return $response;
        }

        $response = $this->getResponse();
        $event->setResponse($response);

        return $this->completeRequest($event);
    }

    /**
     * Complete the request
     *
     * Triggers "render" and "finish" events, and returns response from
     * event object.
     * 
     * @param  MvcEvent $event 
     * @return Response
     */
    protected function completeRequest(MvcEvent $event)
    {
        $events = $this->events();
        $events->trigger('render', $event);
        $events->trigger('finish', $event);
        return $event->getResponse();
    }

    /**
     * Route the request
     *
     * @param  MvcEvent $e
     * @return Router\RouteMatch
     */
    public function route(MvcEvent $e)
    {
        $request = $e->getRequest();
        $router  = $e->getRouter();

        $routeMatch = $router->match($request);

        if (!$routeMatch instanceof Router\RouteMatch) {
            $e->setError(static::ERROR_CONTROLLER_NOT_FOUND);

            $results = $this->events()->trigger('dispatch.error', $e);
            if (count($results)) {
                $return  = $results->last();
            } else {
                $return = $error->getParams();
            }
            return $return;
        }

        $e->setRouteMatch($routeMatch);
        return $routeMatch;
    }

    /**
     * Dispatch the matched route
     *
     * @param  MvcEvent $e
     * @return mixed
     */
    public function dispatch(MvcEvent $e)
    {
        $locator = $this->getLocator();
        if (!$locator) {
            throw new Exception\MissingLocatorException(
                'Cannot dispatch without a locator'
            );
        }

        $routeMatch     = $e->getRouteMatch();
        $controllerName = $routeMatch->getParam('controller', 'not-found');
        $events         = $this->events();

        try {
            $controller = $locator->get($controllerName);
        } catch (ClassNotFoundException $exception) {
            $error = clone $e;
            $error->setError(static::ERROR_CONTROLLER_NOT_FOUND)
                  ->setController($controllerName)
                  ->setParam('exception', $exception);

            $results = $events->trigger('dispatch.error', $error);
            if (count($results)) {
                $return  = $results->last();
            } else {
                $return = $error->getParams();
            }
            goto complete;
        }

        if ($controller instanceof LocatorAware) {
            $controller->setLocator($locator);
        }

        if (!$controller instanceof Dispatchable) {
            $error = clone $e;
            $error->setError(static::ERROR_CONTROLLER_INVALID)
                  ->setController($controllerName)
                  ->setControllerClass(get_class($controller));

            $results = $events->trigger('dispatch.error', $error);
            if (count($results)) {
                $return  = $results->last();
            } else {
                $return = $error->getParams();
            }
            goto complete;
        }

        $request  = $e->getRequest();
        $response = $this->getResponse();

        if ($controller instanceof InjectApplicationEvent) {
            $controller->setEvent($e);
        }

        try {
            $return   = $controller->dispatch($request, $response);
        } catch (\Exception $ex) {
            $error = clone $e;
            $error->setError(static::ERROR_EXCEPTION)
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

        complete:

        if (!is_object($return)) {
            if (IsAssocArray::test($return)) {
                $return = new ArrayObject($return, ArrayObject::ARRAY_AS_PROPS);
            }
        }
        $e->setResult($return);
        return $return;
    }

    /**
     * Attach default listeners for route and dispatch events
     *
     * @param  EventCollection $events
     * @return void
     */
    protected function attachDefaultListeners()
    {
        $events = $this->events();
        $events->attach('route', array($this, 'route'));
        $events->attach('dispatch', array($this, 'dispatch'));
    }
}
