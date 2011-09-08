<?php

namespace Zf2Mvc\Controller;

use ArrayObject,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Http\Response as HttpResponse,
    Zend\Stdlib\Dispatchable,
    Zend\Stdlib\IsAssocArray,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response;

/**
 * Basic action controller
 */
abstract class ActionController implements Dispatchable
{
    protected $events;
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
     * @param  Response $response 
     * @return Response|mixed
     */
    public function dispatch(Request $request, Response $response = null)
    {
        $this->request = $request;
        if (!$response) {
            $response = new HttpResponse();
        }
        $this->response = $response;

        $events = $this->events();
        $params = compact('request', 'response');
        $result = $events->triggerUntil('dispatch.pre', $this, $params, function($test) {
            return ($test instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        $routeMatch = $request->getMetadata('route-match', false);
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

        $params['__RESULT__'] = $actionResponse;
        $result = $events->triggerUntil('dispatch.post', $this, $params, function($test) {
            return ($test instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        return $params['__RESULT__'];
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
        }
        return $this->events;
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
