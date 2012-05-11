<?php

namespace Zend\Mvc;

use ArrayObject;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Http\Header\Cookie;
use Zend\Http\PhpEnvironment\Request as PhpHttpRequest;
use Zend\Http\PhpEnvironment\Response as PhpHttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ConfigurationInterface as InstanceConfigurationInterface;
use Zend\Uri\Http as HttpUri;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

/**
 * Main application class for invoking applications
 *
 * Expects the user will provide a Service Locator or Dependency Injector, as
 * well as a configured router. Once done, calling run() will invoke the
 * application, first routing, then dispatching the discovered controller. A
 * response will then be returned, which may then be sent to the caller.
 */
class Application implements
    ApplicationInterface,
    EventManagerAwareInterface
{
    const ERROR_CONTROLLER_CANNOT_DISPATCH = 'error-controller-cannot-dispatch';
    const ERROR_CONTROLLER_NOT_FOUND       = 'error-controller-not-found';
    const ERROR_CONTROLLER_INVALID         = 'error-controller-invalid';
    const ERROR_EXCEPTION                  = 'error-exception';
    const ERROR_ROUTER_NO_MATCH            = 'error-router-no-match';

    protected $event;

    protected $request;
    protected $response;
    protected $router;

    /**
     * @var EventManager
     */
    protected $events;

    /**
     * @var array
     */
    protected $configuration = null;

    /**
     * @var ServiceManager
     */
    protected $serviceManager = null;

    /**
     * @var \Zend\Module\Manager
     */
    protected $moduleManager;


    public function __construct($configuration, ServiceManager $instanceManager = null)
    {
        $this->configuration = $configuration;

//        if ((null === $instanceManager) && isset($configuration['instance_manager'])) {
//            if ($configuration['instance_manager'] instanceof ServiceManager) {
//                $instanceManager = $configuration['instance_manager'];
//            }
//            if (is_string($configuration['instance_manager']) && class_exists($configuration['instance_manager'])) {
//                $instanceManager = new $configuration['instance_manager']();
//            }
//        }
//        if (null === $instanceManager) {
//            $instanceManager = new ServiceManager();
//        }
//
//        if (!isset($configuration['use_application_manager']) || $configuration['use_application_manager']) {
//            $appManager = new ApplicationManager($configuration);
//            $appManager->configureInstanceManager($instanceManager);
//        }

        $this->setEventManager($instanceManager->get('EventManager'));
        $this->moduleManager = $instanceManager->get('ModuleManager');

        $this->events->attach($instanceManager->get('RouteListener'));
        $this->events->attach($instanceManager->get('DispatchListener'));
        $this->events->attach($instanceManager->get('ViewDefaultRenderingStrategy'));
        $this->request  = $instanceManager->get('Request');
        $this->response = $instanceManager->get('Response');

        $this->serviceManager = $instanceManager;
    }

    public function getConfiguration()
    {
        $this->serviceManager->get('config');
    }

    public function bootstrap()
    {
        $serviceManager = $this->serviceManager;
        $events          = $this->events();
        $sharedEvents    = $events->getSharedManager();

        // Setup error strategies
        $noRouteStrategy   = $serviceManager->get('ViewRouteNotFoundStrategy');
        $exceptionStrategy = $serviceManager->get('ViewExceptionStrategy');
        $events->attach($noRouteStrategy);
        $events->attach($exceptionStrategy);

        // Setup default view events
        $createViewModelListener = new View\CreateViewModelListener();
        $injectTemplateListener  = new View\InjectTemplateListener();
        $injectViewModelListener = new View\InjectViewModelListener();

        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($createViewModelListener, 'createViewModelFromArray'), -80);
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($noRouteStrategy, 'prepareNotFoundViewModel'), -90);
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($createViewModelListener, 'createViewModelFromNull'), -80);
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($injectTemplateListener, 'injectTemplate'), -90);
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($injectViewModelListener, 'injectViewModel'), -100);
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($injectViewModelListener, 'injectViewModel'), -100);

        // Setup MVC Event
        $this->event = $event  = new MvcEvent();
        $event->setTarget($this);
        $event->setApplication($this)
            ->setRequest($this->getRequest())
            ->setResponse($this->getResponse())
            ->setRouter($serviceManager->get('Router'));

        // Setup "layout" view model for event
        $renderingStrategy = $serviceManager->get('ViewDefaultRenderingStrategy');
        $viewModel         = $event->getViewModel();
        $viewModel->setTemplate($renderingStrategy->getLayoutTemplate());

        $renderer    = $this->serviceManager->get('ViewPhpRenderer');
        $modelHelper = $renderer->plugin('view_model');
        $modelHelper->setRoot($viewModel);

        // Trigger bootstrap events
        $this->events()->trigger('bootstrap', $event);
        return $this;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
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
        return $this->response;
    }

    /**
     * Get the router object
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Get the MVC event instance
     *
     * @return MvcEvent
     */
    public function getMvcEvent()
    {
        return $this->event;
    }

    /**
     * Set the event manager instance
     *
     * @param  EventCollection $eventManager
     * @return Application
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
            'application',
        ));
        $this->events = $eventManager;
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
                $event->setTarget($this);
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
            $event->setTarget($this);
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
        $event->setTarget($this);
        $events->trigger('render', $event);
        $events->trigger('finish', $event);
        return $event->getResponse();
    }
}
