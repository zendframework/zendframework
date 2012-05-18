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
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface as Event;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventsCapableInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Loader\Broker;
use Zend\Loader\Pluggable;
use Zend\Mvc\Exception;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

/**
 * Abstract RESTful controller
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class RestfulController implements 
    Dispatchable,
    EventManagerAwareInterface,
    EventsCapableInterface,
    InjectApplicationEventInterface,
    ServiceLocatorAwareInterface,
    Pluggable
{
    /**
     * @var Broker
     */
    protected $broker;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Event
     */
    protected $event;

    /**
     * @var EventCollection
     */
    protected $events;

    /**
     * @var Locator
     */
    protected $locator;

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

        $e = $this->getEvent();
        $e->setRequest($request)
          ->setResponse($response)
          ->setTarget($this);

        $result = $this->events()->trigger(MvcEvent::EVENT_DISPATCH, $e, function($test) {
            return ($test instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        return $e->getResult();
    }

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

        $request = $e->getRequest();
        $action  = $routeMatch->getParam('action', false);
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
                    if (null !== $id = $routeMatch->getParam('id')) {
                        $return = $this->get($id);
                        break;
                    }
                    if (null !== $id = $request->query()->get('id')) {
                        $return = $this->get($id);
                        break;
                    }
                    $return = $this->getList();
                    break;
                case 'post':
                    $return = $this->create($request->post()->toArray());
                    break;
                case 'put':
                    if (null === $id = $routeMatch->getParam('id')) {
                        if (!($id = $request->query()->get('id', false))) {
                            throw new \DomainException('Missing identifier');
                        }
                    }
                    $content = $request->getContent();
                    parse_str($content, $parsedParams);
                    $return = $this->update($id, $parsedParams);
                    break;
                case 'delete':
                    if (null === $id = $routeMatch->getParam('id')) {
                        if (!($id = $request->query()->get('id', false))) {
                            throw new \DomainException('Missing identifier');
                        }
                    }
                    $return = $this->delete($id);
                    break;
                default:
                    throw new \DomainException('Invalid HTTP method!');
            }
        }

        // Emit post-dispatch signal, passing:
        // - return from method, request, response
        // If a listener returns a response object, return it immediately
        $e->setResult($return);
        return $return;
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
     * @param  EventManagerInterface $events
     * @return RestfulController
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            'Zend\Stdlib\DispatchableInterface',
            __CLASS__,
            get_class($this)
        ));
        $this->events = $events;
        $this->attachDefaultListeners();
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function events()
    {
        if (!$this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    /**
     * Set an event to use during dispatch
     *
     * By default, will re-cast to MvcEvent if another event type is provided.
     *
     * @param  Event $e
     * @return void
     */
    public function setEvent(Event $e)
    {
        if ($e instanceof Event && !$e instanceof MvcEvent) {
            $eventParams = $e->getParams();
            $e = new MvcEvent();
            $e->setParams($eventParams);
            unset($eventParams);
        }
        $this->event = $e;
    }

    /**
     * Get the attached event
     *
     * Will create a new MvcEvent if none provided.
     *
     * @return Event
     */
    public function getEvent()
    {
        if (!$this->event) {
            $this->setEvent(new MvcEvent());
        }
        return $this->event;
    }

    /**
     * Set locator instance
     *
     * @param  ServiceLocatorInterface $locator
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Retrieve locator instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->locator;
    }

    /**
     * Get plugin broker instance
     *
     * @return Zend\Loader\Broker
     */
    public function getBroker()
    {
        if (!$this->broker) {
            $this->setBroker(new PluginBroker());
        }
        return $this->broker;
    }

    /**
     * Set plugin broker instance
     *
     * @param  string|Broker $broker Plugin broker to load plugins
     * @return Zend\Loader\Pluggable
     */
    public function setBroker($broker)
    {
        if (!$broker instanceof Broker) {
            throw new Exception\InvalidArgumentException('Broker must implement Zend\Loader\Broker');
        }
        $this->broker = $broker;
        if (method_exists($broker, 'setController')) {
            $this->broker->setController($this);
        }
        return $this;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $plugin  Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return mixed
     */
    public function plugin($name, array $options = null)
    {
        return $this->getBroker()->load($name, $options);
    }

    /**
     * Method overloading: return/call plugins
     *
     * If the plugin is a functor, call it, passing the parameters provided.
     * Otherwise, return the plugin instance.
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $params);
        }
        return $plugin;
    }

    /**
     * Register the default events for this controller
     *
     * @return void
     */
    protected function attachDefaultListeners()
    {
        $events = $this->events();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'execute'));
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
