<?php
namespace Zend\Mvc;

use Zend\Di\Configuration as DiConfiguration,
    Zend\Di\Di,
    Zend\EventManager\EventCollection as Events,
    Zend\EventManager\EventManager,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Manager as ModuleManager,
    Zend\Mvc\Router\Http\TreeRouteStack as Router;

class Bootstrap implements Bootstrapper
{
    /**
     * @var \Zend\Config\Config
     */
    protected $config;

    /**
     * @var ModuleManager
     */
    protected $modules;

    /**
     * @var EventCollection
     */
    protected $events;

    /**
     * Constructor
     *
     * Populates $config from the $modules "getMergedConfig" method.
     * 
     * @param  ModuleManager $modules 
     * @return void
     */
    public function __construct(ModuleManager $modules)
    {
        $this->modules = $modules; 
        $this->config  = $modules->getMergedConfig();
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
        $router = new Router();
        $router->addRoutes($this->config->routes);
        $application->setRouter($router);
    }

    /**
     * Trigger the "bootstrap" event
     *
     * Triggers with the keys "application" and "modules", the latter pointing
     * to the Module Manager attached to the bootstrap.
     * 
     * @param  AppContext $application 
     * @return void
     */
    protected function setupEvents(AppContext $application)
    {
        $params = array(
            'application' => $application,
            'modules'     => $this->modules,
        );
        $this->events()->trigger('bootstrap', $this, $params);
    }
}
