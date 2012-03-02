<?php
namespace Zend\Mvc;

use Zend\Di\Configuration as DiConfiguration,
    Zend\Di\Di,
    Zend\Config\Config,
    Zend\EventManager\EventCollection as Events,
    Zend\EventManager\EventManager,
    Zend\EventManager\StaticEventManager,
    Zend\Mvc\Router\Http\TreeRouteStack as Router;

class Bootstrap implements Bootstrapper
{
    /**
     * @var \Zend\Config\Config
     */
    protected $config;

    /**
     * @var EventCollection
     */
    protected $events;

    /**
     * Constructor
     *
     * @param Config $config 
     * @return void
     */
    public function __construct(Config $config)
    {
        $this->config = $config; 
    }

    /**
     * Set the event manager to use with this object
     * 
     * @param  Events $events 
     * @return void
     */
    public function setEventManager(Events $events)
    {
        $this->events = $events;
    }

    /**
     * Retrieve the currently set event manager
     *
     * If none is initialized, an EventManager instance will be created with
     * the contexts of this class, the current class name (if extending this
     * class), and "bootstrap".
     * 
     * @return Events
     */
    public function events()
    {
        if (!$this->events instanceof Events) {
            $this->setEventManager(new EventManager(array(
                __CLASS__,
                get_called_class(),
                'bootstrap',
            )));
        }
        return $this->events;
    }

    /**
     * Bootstrap the application
     *
     * - Initializes the locator, and injects it in the application
     * - Initializes the router, and injects it in the application
     * - Triggers the "bootstrap" event, passing in the application and modules 
     *   as parameters. This allows module classes to perform arbitrary
     *   initialization tasks after bootstrapping but before running the 
     *   application.
     * 
     * @param Application $application 
     * @return void
     */
    public function bootstrap(AppContext $application)
    {
        $this->setupLocator($application);
        $this->setupRouter($application);
        $this->setupView($application);
        $this->setupEvents($application);
    }


    /**
     * Sets up the locator based on the configuration provided
     * 
     * @param  AppContext $application 
     * @return void
     */
    protected function setupLocator(AppContext $application)
    {
        $di = new Di;
        $di->instanceManager()->addTypePreference('Zend\Di\Locator', $di);

        // Default configuration for common MVC classes
        $diConfig = new DiConfiguration(array('definition' => array('class' => array(
            'Zend\Mvc\Router\RouteStack' => array(
                'instantiator' => array(
                    'Zend\Mvc\Router\Http\TreeRouteStack',
                    'factory'
                ),
            ),
            'Zend\Mvc\Router\Http\TreeRouteStack' => array(
                'instantiator' => array(
                    'Zend\Mvc\Router\Http\TreeRouteStack',
                    'factory'
                ),
            ),
            'Zend\Mvc\View\DefaultRenderingStrategy' => array(
                'setLayoutTemplate' => array(
                    'layoutTemplate' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
            ),
            'Zend\Mvc\View\ExceptionStrategy' => array(
                'setDisplayExceptions' => array(
                    'displayExceptions' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
                'setExceptionTemplate' => array(
                    'exceptionTemplate' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
            ),
            'Zend\Mvc\View\RouteNotFoundStrategy' => array(
                'setDisplayNotFoundReason' => array(
                    'displayNotFoundReason' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
                'setNotFoundTemplate' => array(
                    'notFoundTemplate' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
            ),
            'Zend\View\HelperBroker' => array(
                'setClassLoader' => array(
                    'required' => true,
                    'loader'   => array(
                        'type'     => 'Zend\View\HelperLoader',
                        'required' => true,
                    ),
                ),
            ),
            'Zend\View\HelperLoader' => array(
                'registerPlugins' => array(
                    'map' => array(
                        'type'     => false,
                        'required' => false,
                    ),
                ),
            ),
            'Zend\View\Renderer\PhpRenderer' => array(
                'setBroker' => array(
                    'required' => true,
                    'broker'   => array(
                        'type'     => 'Zend\View\HelperBroker',
                        'required' => true,
                    ),
                ),
                'setCanRenderTrees' => array(
                    'required' => false,
                    'renderTrees' => array(
                        'type'     => false,
                        'required' => true,
                    ),
                ),
                'setResolver' => array(
                    'required' => false,
                    'resolver' => array(
                        'type'     => 'Zend\View\Resolver',
                        'required' => true,
                    ),
                ),
            ),
            'Zend\View\Resolver\AggregateResolver' => array(
                'attach' => array(
                    'resolver' => array(
                        'required' => false,
                        'type'     => 'Zend\View\Resolver',
                    ),
                ),
            ),
            'Zend\View\Resolver\TemplatePathStack' => array(
                'setDefaultSuffix' => array(
                    'defaultSuffix' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
                'setPaths' => array(
                    'paths' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
            ),
            'Zend\View\Strategy\PhpRendererStrategy' => array(
                'setContentPlaceholders' => array(
                    'contentPlaceholders' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
            ),
        ))));
        $diConfig->configure($di);

        $config = new DiConfiguration($this->config->di);
        $config->configure($di);

        $application->setLocator($di);
    }

    /**
     * Sets up the router based on the configuration provided
     * 
     * @param  Application $application 
     * @return void
     */
    protected function setupRouter(AppContext $application)
    {
        $router = $application->getLocator()->get('Zend\Mvc\Router\RouteStack');
        $application->setRouter($router);
    }

    /**
     * Sets up the view integration
     *
     * Pulls the View object and PhpRenderer strategy from the locator, and 
     * attaches the former to the latter. Then attaches the 
     * DefaultRenderingStrategy to the application event manager.
     * 
     * @param  Application $application 
     * @return void
     */
    protected function setupView($application)
    {
        // Basic view strategy
        $locator             = $application->getLocator();
        $events              = $application->events();
        $staticEvents        = StaticEventManager::getInstance();
        $view                = $locator->get('Zend\View\View');
        $phpRendererStrategy = $locator->get('Zend\View\Strategy\PhpRendererStrategy');
        $defaultViewStrategy = $locator->get('Zend\Mvc\View\DefaultRenderingStrategy');
        $view->events()->attachAggregate($phpRendererStrategy);
        $events->attachAggregate($defaultViewStrategy);

        // Error strategies
        $noRouteStrategy   = $locator->get('Zend\Mvc\View\RouteNotFoundStrategy');
        $exceptionStrategy = $locator->get('Zend\Mvc\View\ExceptionStrategy');
        $events->attachAggregate($noRouteStrategy);
        $events->attachAggregate($exceptionStrategy);

        // Template/ViewModel listeners
        $arrayListener           = $locator->get('Zend\Mvc\View\CreateViewModelFromArrayListener');
        $injectTemplateListener  = $locator->get('Zend\Mvc\View\InjectTemplateListener');
        $injectViewModelListener = $locator->get('Zend\Mvc\View\InjectViewModelListener');
        $staticEvents->attach('Zend\Stdlib\Dispatchable', 'dispatch', array($arrayListener, 'createViewModelFromArray'), -80);
        $staticEvents->attach('Zend\Stdlib\Dispatchable', 'dispatch', array($injectTemplateListener, 'injectTemplate'), -90);
        $events->attach('dispatch.error', array($injectViewModelListener, 'injectViewModel'), -100);
        $staticEvents->attach('Zend\Stdlib\Dispatchable', 'dispatch', array($injectViewModelListener, 'injectViewModel'), -100);

        // Inject MVC Event with view model
        $mvcEvent  = $application->getMvcEvent();
        $viewModel = $mvcEvent->getViewModel();
        $viewModel->setTemplate($defaultViewStrategy->getLayoutTemplate());

        // Inject MVC Event view model as root view model
        $renderer    = $phpRendererStrategy->getRenderer();
        $modelHelper = $renderer->plugin('view_model');
        $modelHelper->setRoot($viewModel);
    }

    /**
     * Trigger the "bootstrap" event
     *
     * Triggers with the keys "application" and "config", the latter pointing
     * to the Module Manager attached to the bootstrap.
     * 
     * @param  AppContext $application 
     * @return void
     */
    protected function setupEvents(AppContext $application)
    {
        $params = array(
            'application' => $application,
            'config'      => $this->config,
        );
        $this->events()->trigger('bootstrap', $this, $params);
    }
}
