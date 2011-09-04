<?php

namespace Zf2Mvc\Controller;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
    Zend\Stdlib\Dispatchable,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response;

/**
 * Abstract RESTful controller
 */
abstract class RestfulController implements Dispatchable
{
    protected $request;
    protected $response;
    protected $events;

    /**
     * Return list of resources
     * 
     * @return mixed
     */
    abstract public function getList();

    /**
     * Return single resource
     * 
     * @param  mixed $id 
     * @return mixed
     */
    abstract public function get($id);

    /**
     * Create a new resource
     * 
     * @param  mixed $data 
     * @return mixed
     */
    abstract public function create($data);

    /**
     * Update an existing resource
     * 
     * @param  mixed $id 
     * @param  mixed $data 
     * @return mixed
     */
    abstract public function update($id, $data);

    /**
     * Delete an existing resource
     * 
     * @param  mixed $id 
     * @return mixed
     */
    abstract public function delete($id);

    /**
     * Basic functionality for when a page is not available
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
     * If the route match includes an "action" key, then this acts basically like
     * a standard action controller. Otherwise, it introspects the HTTP method
     * to determine how to handle the request, and which method to delegate to.
     * 
     * @events dispatch.pre, dispatch.post
     * @param  Request $request 
     * @param  null|Response $response 
     * @return mixed|Response
     */
    public function dispatch(Request $request, Response $response = null)
    {
        if (!$request instanceof HttpRequest) {
            throw new \InvalidArgumentException('Expected an HTTP request');
        }
        $this->request = $request;
        if (!$response) {
            $response = new HttpResponse();
        }
        $this->response = $response;

        // Emit pre-dispatch signal, passing:
        // - request, response
        // If a handler returns a response object, return it immediately
        $events = $this->events();
        $params = compact('request', 'response');
        $result = $events->triggerUntil('dispatch.pre', $this, $params, function($result) {
            return ($result instanceof Response);
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
        $action = $routeMatch->getParam('action', false);
        if ($action) {
            // Handle arbitrary methods, ending in Action
            $method = static::getMethodFromAction($action);
            if (!method_exists($this, $method)) {
                $method = 'notFoundAction';
            }
            $return = $this->$method();
        } else {
            // RESTful methods
            switch (strtolower($request->getMethod())) {
                case 'get':
                    if (null !== $id = $request->getMetadata('id')) {
                        $return = $this->get($id);
                        break;
                    }
                    $return = $this->getList();
                    break;
                case 'post':
                    $return = $this->create($request->post()->toArray());
                    break;
                case 'put':
                    if (null === $id = $request->getMetadata('id')) {
                        throw new \DomainException('Missing identifier');
                    }
                    $params = $request->getContent();
                    $params = parse_str($params);
                    $return = $this->update($id, $params);
                    break;
                case 'delete':
                    if (null === $id = $request->getMetadata('id')) {
                        throw new \DomainException('Missing identifier');
                    }
                    $return = $this->delete($id);
                    break;
                default:
                    throw new \DomainException('Invalid HTTP method!');
            }
        }

        // Emit post-dispatch signal, passing:
        // - return from method, request, response
        // If a handler returns a response object, return it immediately
        $params['__RESULT__'] =& $return;
        $result = $events->triggerUntil(__FUNCTION__ . '.post', $this, $params, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        return $params['__RESULT__'];
    }

    /**
     * Get request object
     *
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->setRequest(new HttpRequest());
        }
        return $this->request;
    }

    /**
     * Get response object
     *
     * @return Response
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->setResponse(new HttpResponse());
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
        if (!$this->events) {
            $this->setEventManager(new EventManager(array(
                'Zend\Stdlib\Dispatchable',
                __CLASS__,
                get_called_class(),
            )));
        }
        return $this->events;
    }
    
    /**
     * Transform an "action" token into a method name
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
