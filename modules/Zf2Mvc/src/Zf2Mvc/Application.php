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
        }
        return $this->events;
    }

    /**
     * Run the application
     * 
     * @events route.pre, route.post, dispatch.pre, dispatch.post, dispatch.error
     * @return Response
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
            $response = $result;
        } else {
            $response = $this->getResponse();
        }
        $response = new SendableResponse($response);
        return $response;
    }

    /**
     * Route the request
     * 
     * @events route.pre, route.post
     * @return Router\RouteMatch
     */
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

    /**
     * Dispatch the matched route
     * 
     * @events dispatch.pre, dispatch.post, dispatch.error
     * @param  Router\RouteMatch $routeMatch 
     * @return mixed
     */
    protected function dispatch($routeMatch)
    {
        $events  = $this->events();
        $params  = compact('routeMatch');
        $result  = $events->triggerUntil('dispatch.pre', $this, $params, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        $controllerName = $routeMatch->getParam('controller', 'not-found');
        $locator        = $this->getLocator();

        try {
            $controller = $locator->get($controllerName);
        } catch (ClassNotFoundException $e) {
            $errorParams = array(
                'error'       => static::ERROR_CONTROLLER_NOT_FOUND,
                'controller'  => $controllerName,
                'route-match' => $routeMatch,
            );
            $results = $events->trigger('dispatch.error', $this, $errorParams);
            if (count($results)) {
                $return  = $results->last();
            } else {
                $return = $errorParams;
            }
            goto complete;
        }

        if (!$controller instanceof Dispatchable) {
            $errorParams = array(
                'error'            => static::ERROR_CONTROLLER_INVALID,
                'controller'       => $controllerName,
                'controller-class' => get_class($controller),
                'route-match'      => $routeMatch,
            );
            $results = $events->trigger('dispatch.error', $this, $errorParams);
            if (count($results)) {
                $return  = $results->last();
            } else {
                $return = $errorParams;
            }
            goto complete;
        }

        $request  = $this->getRequest();
        $request->setMetadata('route-match', $routeMatch);
        $response = $this->getResponse();
        $return   = $controller->dispatch($request, $response);

        complete:

        if (!is_object($return)) {
            if (IsAssocArray::test($return)) {
                $return = new ArrayObject($return, ArrayObject::ARRAY_AS_PROPS);
            }
        }

        $params['__RESULT__'] = $return;
        $result  = $events->triggerUntil('dispatch.post', $this, $params, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        return $params['__RESULT__'];
    }
}
