<?php

namespace Zend\Mvc\Controller;

use ArrayObject,
    Zend\Di\Locator,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventDescription as Event,
    Zend\EventManager\EventManager,
    Zend\Http\Response as HttpResponse,
    Zend\Stdlib\Dispatchable,
    Zend\Stdlib\IsAssocArray,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\Mvc\LocatorAware,
    Zend\Mvc\MvcEvent;

/**
 * Basic action controller
 */
abstract class ActionController implements Dispatchable, LocatorAware
{
    protected $event;
    protected $events;
    protected $locator;
    protected $request;
    protected $response;

    /**
     * Default action if none provided
     * 
     * @return array
     */
    public function indexAction()
    {
        return array('content' => 'Placeholder page');
    }

    /**
     * Action called if matched action does not exist
     * 
     * @return array
     */
    public function notFoundAction()
    {
        $this->response->setStatusCode(404);
        return array('content' => 'Page not found');
    }

    /**
     * Dispatch a request
     * 
     * @events dispatch.pre, dispatch.post
     * @param  Request $request 
     * @param  null|Response $response 
     * @param  null|Event $e
     * @return Response|mixed
     */
    public function dispatch(Request $request, Response $response = null, Event $e = null)
    {
        $this->request = $request;
        if (!$response) {
            $response = new HttpResponse();
        }
        $this->response = $response;

        if ($e instanceof Event && !$e instanceof MvcEvent) {
            $eventParams = $e->getParams();
            $e = new MvcEvent();
            $e->setParams($eventParams);
            unset($eventParams);
        }
        if (null === $e) {
            $e = new MvcEvent();
        }
        $e->setRequest($request)
          ->setResponse($response)
          ->setTarget($this);
        $this->event = $e;

        $result = $this->events()->trigger('dispatch', $e, function($test) {
            return ($test instanceof Response);
        });

        if ($result->stopped()) {
            return $result->last();
        }
        return $e->getResult();
    }

    /**
     * Execute the request
     * 
     * @param  MvcEvent $e 
     * @return mixed
     */
    public function execute(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            /**
             * @todo Determine requirements for when route match is missing.
             *       Potentially allow pulling directly from request metadata?
             */
            throw new \DomainException('Missing route matches; unsure how to retrieve action');
        }

        $action = $routeMatch->getParam('action', 'index');
        $method = static::getMethodFromAction($action);

        if (!method_exists($this, $method)) {
            $method = 'notFoundAction';
        }

        $actionResponse = $this->$method();

        if (!is_object($actionResponse)) {
            if (IsAssocArray::test($actionResponse)) {
                $actionResponse = new ArrayObject($actionResponse, ArrayObject::ARRAY_AS_PROPS);
            }
        }

        $e->setResult($actionResponse);
        return $actionResponse;
    }

    /**
     * Get the request object
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the response object
     * 
     * @return Response
     */
    public function getResponse()
    {
        if (null === $this->response) {
            $this->response = new HttpResponse();
        }
        return $this->response;
    }

    /**
     * Retrieve event passed to or created in dispatch()
     * 
     * @return null|MvcEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

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
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     * 
     * @return EventCollection
     */
    public function events()
    {
        if (!$this->events instanceof EventCollection) {
            $this->setEventManager(new EventManager(array(
                'Zend\Stdlib\Dispatchable',
                __CLASS__, 
                get_called_class()
            )));
            $this->registerDefaultEvents();
        }
        return $this->events;
    }

    /**
     * Set locator instance
     * 
     * @param  Locator $locator 
     * @return void
     */
    public function setLocator(Locator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Retrieve locator instance
     * 
     * @return Locator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Register the default events for this controller
     * 
     * @return void
     */
    protected function registerDefaultEvents()
    {
        $events = $this->events();
        $events->attach('dispatch', array($this, 'execute'));
    }

    /**
     * Transform an action name into a method name
     * 
     * @param  string $action 
     * @return string
     */
    public static function getMethodFromAction($action)
    {
        $method  = str_replace(array('.', '-', '_'), ' ', $action);
        $method  = ucwords($method);
        $method  = str_replace(' ', '', $method);
        $method  = lcfirst($method);
        $method .= 'Action';
        return $method;
    }
}
