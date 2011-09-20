<?php

namespace Zf2Mvc;

use ArrayObject,
    Zend\Di\Exception\ClassNotFoundException,
    Zend\Di\Locator,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Http\Header\Cookie,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
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
    const ERROR_CONTROLLER_NOT_FOUND = 404;
    const ERROR_CONTROLLER_INVALID   = 500;

    protected $events;
    protected $defaultListeners = array();
    protected $disableDefaultEventListeners = false;
    protected $locator;
    protected $request;
    protected $response;
    protected $router;

    /**
     * Set the event manager instance used by this context
     * 
     * @param  EventCollection $events 
     * @return AppContext
     */
    public function setEventManager(EventCollection $events)
    {
        $this->attachDefaultListeners($events);
        $this->events = $events;
        return $this;
    }

    /**
     * Set a service locator/DI object
     *
     * @param  Locator $locator 
     * @return AppContext
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
     * @return AppContext
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
     * @return AppContext
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
     * @return AppContext
     */
    public function setRouter(Router\RouteStack $router)
    {
        $this->router = $router;
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
            $request = new HttpRequest();

            $request->setQuery(new PhpEnvironment\GetContainer())
                    ->setPost(new PhpEnvironment\PostContainer())
                    ->setEnv(new Parameters($_ENV))
                    ->setServer(new Parameters($_SERVER));

            if ($_COOKIE) {
                $request->headers()->addHeader(new Cookie($_COOKIE));
            }

            if ($_FILES) {
                $request->setFile(new Parameters($_FILES));
            }

            if (isset($_SERVER['REQUEST_METHOD'])) {
                $request->setMethod($_SERVER['REQUEST_METHOD']);
            }

            if (isset($_SERVER['REQUEST_URI'])) {
                $request->setUri($_SERVER['REQUEST_URI']);
            }

            $this->setRequest($request);
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
            $this->setResponse(new HttpResponse());
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
        }
        return $this->events;
    }

    /**
     * Set flag indicating whether or not to disable the default event listeners
     * 
     * @param mixed $flag 
     * @return Application
     */
    public function setDisableDefaultEventListenersFlag($flag)
    {
        $this->disableDefaultEventListeners = (bool) $flag;
        $this->detachDefaultListeners();
        return $this;
    }

    /**
     * Should we disable the default event listeners?
     * 
     * @return bool
     */
    public function disableDefaultEventListeners()
    {
        return $this->disableDefaultEventListeners;
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
        $locator = $this->getLocator();
        if (!$locator) {
            throw new Exception\MissingLocatorException(
                'Cannot run application without a locator'
            );
        }

        $events = $this->events();
        $event  = new MvcEvent();
        $event->setTarget($this);
        $event->setRequest($this->getRequest())
              ->setRouter($this->getRouter());

        $event->setName('route');
        $events->trigger($event);

        $event->setName('dispatch');
        $result = $events->trigger($event, function ($r) {
            return ($r instanceof Response);
        });

        $response = $result->last();
        if (!$response instanceof Response) {
            $response = $this->getResponse();
        }

        $response = new SendableResponse($response);
        return $response;
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
            /**
             * @todo handle failed routing
             *
             * This might be something we can do via an event
             */
            throw new \Exception('UNIMPLEMENTED: Handling of failed routing');
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
        $events     = $this->events();
        $routeMatch = $e->getRouteMatch();

        $controllerName = $routeMatch->getParam('controller', 'not-found');
        $locator        = $this->getLocator();

        try {
            $controller = $locator->get($controllerName);
        } catch (ClassNotFoundException $e) {
            $error = clone $e;
            $error->setError(static::ERROR_CONTROLLER_NOT_FOUND)
                  ->setController($controllerName)
                  ->setName('dispatch.error');

            $results = $events->trigger($error);
            if (count($results)) {
                $return  = $results->last();
            } else {
                $return = $error->getParams();
            }
            goto complete;
        }

        if (!$controller instanceof Dispatchable) {
            $error = clone $e;
            $error->setError(static::ERROR_CONTROLLER_INVALID)
                  ->setController($controllerName)
                  ->setControllerClass(get_class($controller))
                  ->setName('dispatch.error');
            $results = $events->trigger($error);
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

        $request  = $e->getRequest();
        $response = $this->getResponse();
        $event    = clone $e;
        $return   = $controller->dispatch($request, $response, $e);

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
    protected function attachDefaultListeners(EventCollection $events)
    {
        if ($this->disableDefaultEventListeners()) {
            return;
        }
        $this->defaultListeners[] = $events->attach('route', array($this, 'route'));
        $this->defaultListeners[] = $events->attach('dispatch', array($this, 'dispatch'));
    }

    /**
     * Detach the default listeners for the route and dispatch events, if attached
     * 
     * @return void
     */
    protected function detachDefaultListeners()
    {
        if (!$this->disableDefaultEventListeners || empty($this->defaultListeners)) {
            return;
        }

        $events = $this->events();
        foreach ($this->defaultListeners as $key => $listener) {
            $events->detach($listener);
            unset($this->defaultListeners[$key]);
        }
    }
}
