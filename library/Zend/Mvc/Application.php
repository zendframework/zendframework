<?php

namespace Zend\Mvc;

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
    const ERROR_CONTROLLER_INVALID   = 404;
    const ERROR_EXCEPTION            = 500;

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
        $event  = new MvcEvent();
        $event->setTarget($this);
        $event->setRequest($this->getRequest())
              ->setRouter($this->getRouter());

        $result = $events->trigger('route', $event, function ($r) {
            return ($r instanceof Response);
        });
        if ($result->stopped()) {
            $response = new SendableResponse($result->last());
            return $response;
        }

        $result = $events->trigger('dispatch', $event, function ($r) {
            return ($r instanceof Response);
        });

        $response = $result->last();
        if (!$response instanceof Response) {
            $response = $this->getResponse();
            $event->setResponse($response);
        }

        $events->trigger('finish', $event);

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
            $error = clone $e;
            $error->setError(static::ERROR_CONTROLLER_NOT_FOUND);

            $results = $this->events()->trigger('dispatch.error', $error);
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

        $events     = $this->events();
        $routeMatch = $e->getRouteMatch();

        $controllerName = $routeMatch->getParam('controller', 'not-found');

        try {
            $controller = $locator->get($controllerName);
        } catch (ClassNotFoundException $exception) {
            $error = clone $e;
            $error->setError(static::ERROR_CONTROLLER_NOT_FOUND)
                  ->setController($controllerName);

            $results = $events->trigger('dispatch.error', $error);
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
                  ->setControllerClass(get_class($controller));
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
