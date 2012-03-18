<?php
namespace Zend\Mvc;

use Zend\Di\Configuration as DiConfiguration,
    Zend\Di\Di,
    Zend\Config\Config,
    Zend\EventManager\EventCollection as Events,
    Zend\EventManager\EventManager,
    Zend\EventManager\StaticEventManager,
    Zend\Mvc\Router\Http\TreeRouteStack as Router,
    Zend\Console\Console,
    Zend\Console\Request as ConsoleRequest,
    Zend\Console\Response as ConsoleResponse,
    Zend\View\Model\ConsoleModel;

class ConsoleBootstrap extends Bootstrap implements Bootstrapper
{
    /**
     * Bootstrap the application
     *
     * - Initializes the locator, and injects it in the application
     * - Initializes the router, and injects it in the application
     * - Triggers the "bootstrap" event, passing in the application and modules
     *   as parameters. This allows module classes to perform arbitrary
     *   initialization tasks after bootstrapping but before running the
     *   application.
     * - Initializes Console request and response
     *
     * @param Application $application
     * @return void
     */
    public function bootstrap(AppContext $application)
    {
        parent::bootstrap($application);

        /**
         * Init Console request and response
         */
        $request = new ConsoleRequest();
        $response = new ConsoleResponse();
        $application->setRequest($request);
        $application->setResponse($response);
        $event = $application->getMvcEvent();   // TODO: This should not be required - MVC event is created too soon
        $event->setRequest($request);
        $event->setResponse($response);
        $event->setViewModel(new ConsoleModel());

        /**
         * Inject route params into request
         */

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
            'Zend\Console\Console' => array(
                'instantiator' => array(
                    'Zend\Console\Console',
                    'getInstance'
                ),
            ),
            'Zend\Mvc\Router\RouteStack' => array(
                'instantiator' => array(
                    'Zend\Mvc\Router\SimpleRouteStack',
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
            'Zend\Mvc\View\InjectRoutematchParamsListener' => array(
                'setOverwrite' => array(
                    'overwrite' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                )
            ),
            'Zend\View\Renderer\ConsoleRenderer' => array(
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
            'Zend\View\Strategy\ConsoleStrategy' => array(
                '__construct' => array(
                    'renderer' => array(
                        'type' => 'Zend\View\Renderer\ConsoleRenderer',
                        'required' => true
                    ),
                    'console' => array(
                        'type' => 'Zend\Console\Console',
                        'required' => true
                    )
                )
            ),
        ))));
        $diConfig->configure($di);

        $config = new DiConfiguration($this->config->di);
        $config->configure($di);

        $application->setLocator($di);
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
        $consoleStrategy     = $locator->get('Zend\View\Strategy\ConsoleStrategy');
        $defaultViewStrategy = $locator->get('Zend\Mvc\View\DefaultRenderingStrategy');
        $view->events()->attachAggregate($consoleStrategy);
        $events->attachAggregate($defaultViewStrategy);

        // Error strategies
//        $noRouteStrategy   = $locator->get('Zend\Mvc\View\RouteNotFoundStrategy');
//        $exceptionStrategy = $locator->get('Zend\Mvc\View\ExceptionStrategy');
//        $events->attachAggregate($noRouteStrategy);
//        $events->attachAggregate($exceptionStrategy);

        // Param inject strategy
        $injectStrategy = $locator->get('Zend\Mvc\View\InjectRoutematchParamsListener');
        $events->attachAggregate($injectStrategy);

        // Template/ViewModel listeners
        $createViewModelListener = $locator->get('Zend\Mvc\View\CreateViewModelListener');
//        $injectTemplateListener  = $locator->get('Zend\Mvc\View\InjectTemplateListener');
        $injectViewModelListener = $locator->get('Zend\Mvc\View\InjectViewModelListener');
        $staticEvents->attach('Zend\Stdlib\Dispatchable', 'dispatch', array($createViewModelListener, 'createViewModelFromNull'), -80);
        $staticEvents->attach('Zend\Stdlib\Dispatchable', 'dispatch', array($createViewModelListener, 'createViewModelFromString'), -80);
//        $staticEvents->attach('Zend\Stdlib\Dispatchable', 'dispatch', array($injectTemplateListener, 'injectTemplate'), -90);
        $events->attach('dispatch.error', array($injectViewModelListener, 'injectViewModel'), -100);
        $staticEvents->attach('Zend\Stdlib\Dispatchable', 'dispatch', array($injectViewModelListener, 'injectViewModel'), -100);

        // Inject MVC Event with view model
//        $mvcEvent  = $application->getMvcEvent();
//        $viewModel = $mvcEvent->getViewModel();
//        $viewModel->setTemplate($defaultViewStrategy->getLayoutTemplate());
    }
}