MVC Examples
============

Example 1 - Bootstrapping
-------------------------

While the `Application` class will actually perform execution of the
application, you still need to provide some configuration -- for instance,
setting up the Dependency Injector or Service Locator instance you plan to use,
and providing routes to the router.

The best way to accomplish this is via composition. Consider the following 
`Bootstrap` class. It accepts an `Application` instance to its `bootstrap()` 
method and configures it.

use Zend\Config\Config,
    Zf2Mvc\Application;

    class Bootstrap
    {
        protected $config;

        public function __construct(Config $config)
        {
            $this->config = $config;
        }

        public function bootstrap(Application $app)
        {
            $this->setupLocator($app);
            $this->setupRoutes($app);
            $this->setupEvents($app);
        }

        protected function setupLocator(Application $app)
        {
            /**
             * Instantiate and configure a DependencyInjector instance, or 
             * a ServiceLocator, and return it.
             */
        }

        protected function setupRoutes(Application $app)
        {
            /**
             * Pull the routing table from configuration, and pass it to the
             * router composed in the Application instance.
             */
        }

        protected function setupEvents(Application $app)
        {
            /**
             * Wire events into the Application's EventManager, and/or setup
             * static listeners for events that may be invoked.
             */
        }
    }

From here, the gateway script, `public/index.php` might look like this:

    // Assume autoloading is configured

    $env = getenv('APPLICATION_ENV');
    if (!$env) {
        $env = 'production';
    }

    $config    = Zend\Config\Factory::factory(
        __DIR__ . '/../configs/' . $env . '.config.xml'
    );
    $app       = new Zf2Mvc\Application();
    $bootstrap = new Bootstrap();
    $bootstrap->bootstrap($app);

    $response = $app->run();
    $response->send();

Example 2 - Controllers
-----------------------

Controllers are simply classes that implement `Zend\Stdlib\Dispatchable`. As
such, it's up to the developer to determine how they handle a request.

To hand off the request to the controller requires two wirings:

*   A route that, when matched, returns a controller name
*   A DI manager that composes a definition that includes that controller name, 
    or composes configuration that aliases the controller name to a valid class
    in the composed DI definition.

Let's assume we have a "Hello" controller:

    namespace HelloWorld\Controller;

    use Zend\Http\Response as HttpResponse,
        Zend\Stdlib\Dispatchable,
        Zend\Stdlib\RequestDescription as Request,
        Zend\Stdlib\ResponseDescription as Response;

    class HelloController implements Dispatchable
    {
        public function dispatch(Request $request, Response $response = null)
        {
            if (null === $response) {
                $response = new HttpResponse();
            }
            $response->setContent('<h1>Hello, world!</h1>');
            return $response;
        }
    }

Let's now create a route:

    use Zend\Http\Router\Http\LiteralRoute;

    // Assume this is likely in some bootstrap
    $route = new LiteralRoute(array(
        'route'    => '/hello',
        'defaults' => array(
            'controller' => 'controller.hello-world.hello',
        ),
    ));
    $app->getRouter()->addRoute('hello', $route);

Finally, our DI configuration needs to know about the controller.

    use Zend\Di\Configuration;

    $config = new Configuration(array(
        'di' => array(
            'instance' => array(
                'alias' => array(
                    'controller.hello-world.hello' => 'HelloWorld\Controller\HelloController',
                ),
            ),
        ),
    ));
    $config->configure($di); // assuming we've created it previously

Once these are in place, when navigating to the url "/hello", we'll now execute
our controller's `dispatch()` method.
