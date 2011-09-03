<?php

namespace Zf2Mvc;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Stdlib\Dispatchable,
    Zend\Http\Request,
    Zend\Http\Response;

class Application implements AppContext
{
    protected $events;
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
        $this->events = $events;
        return $this;
    }

    /**
     * Set a service locator object
     *
     * Since the DI DependencyInjection and ServiceLocation objects do not 
     * share a common interface, we will not specify an interface here. That
     * said, both implement the same "get()" method signature, and this is 
     * what we will enforce.
     * 
     * @param  mixed $locator 
     * @return AppContext
     */
    public function setLocator($locator)
    {
        if (!is_object($locator)) {
            throw new Exception\InvalidArgumentException('Locator must be an object');
        }
        if (!method_exists($locator, 'get')) {
            throw new Exception\InvalidArgumentException('Locator must implement a "get()" method');
        }
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
     * @return null|object
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
            $this->setRequest(new Request());
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
            $this->setResponse(new Response());
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
     * Run the application
     * 
     * @return \Zend\Http\Response
     */
    public function run()
    {
        $locator = $this->getLocator();
        if (!$locator) {
            throw new Exception\MissingLocatorException(
                'Cannot run application without a locator'
            );
        }

        $routeMatch = $this->route();
        $result     = $this->dispatch($routeMatch);

        if ($result instanceof Response) {
            return $result;
        }

        return $this->getResponse();
    }

    protected function route()
    {
        $request = $this->getRequest();
        $router  = $this->getRouter();
        $events  = $this->events();
        $params  = compact('request', 'router');

        $events->trigger('route.pre', $this, $params);

        $routeMatch = $router->match($request);

        if (!$routeMatch instanceof Router\RouteMatch) {
            /**
             * @todo handle failed routing
             *
             * This might be something we can do via an event
             */
            throw new \Exception('UNIMPLEMENTED: Handling of failed routing');
        }

        $params['__RESULT__'] = $routeMatch;
        $events->trigger('route.post', $this, $params);
        return $routeMatch;
    }

    protected function dispatch($routeMatch)
    {
        $events  = $this->events();
        $params  = compact('routeMatch');
        $events->trigger('dispatch.pre', $this, $params);

        $controllerName = $routeMatch->getParam('controller', 'not-found');
        $locator        = $this->getLocator();
        $controller     = $locator->get($controllerName);

        if (!$controller instanceof Dispatchable) {
            /**
             * @todo handle undispatchable controller
             *
             * This might be something to handle via an event?
             */
            throw new \Exception('UNIMPLEMENTED: Handling not-found controller');
        }

        $request  = $this->getRequest();
        $response = $this->getResponse();
        $result   = $controller->dispatch($request, $response);

        $params['result'] =& $result;
        $events->trigger('dispatch.post', $this, $params);
        return $result;
    }
}
