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

While this approach is portable and allows you to handle any request, we doubt
you'll want to write `dispatch()` logic for each and every controller you write,
much less each and every action. As such, this module also includes two base
controllers you can extend, the `ActionController` and the `RestfulController`.

h3. The ActionController

`Zf2Mvc\Controller\ActionController` is very similar to `Zend_Controller_Action`
in the Zend Framework 1.X series. It checks for a matched "action" token in the
returned array from a router, and then maps this to a corresponding "Action" 
method. As some examples:

* Action "foo" maps to method "fooAction"
* Action "foo-bar" maps to method "fooBarAction"
* Action "bar.baz" maps to method "barBazAction"
* Action "baz\_.bat" maps to method "bazBatAction"

As you can see, the simple rule is that "-", ".", and "\_" all become word 
separators, and the individual words are camelCased and appended with the word
"Action".

The "action" token is something your route will discover. For instance given a 
standard route of:

    /:controller/:action

and the invoked URL:

    /foo/bar

the assumption is that the controller name is "foo" and will map to a 
Dispatchable class, and the action name is "bar". These values are returned by
the router as members of an instance of `Zf2Mvc\Router\RouteMatch`. That class
allows you to pull such members using the method `getParam()`, which also 
allows you to specify a default value to return if the parameter is not found.

    $action = $routeMatch->getParam('action', false);
    if (!$action) {
        // No action found!
        // ...
    }

Two special actions are defined by default, "index" and "not-found". The former
is used whenever an ActionController is invoked without an "action" parameter
in the route match. The latter is invoked if the discovered action does not map
to an existing method in the controller. 

Normal development using an `ActionController` is very straight-forward: you 
identify needed functionality, and simply create an action method for that
endpoint. As an example, let's return to the "/foo/bar" URL from earlier:

    namespace SomeModule\Controller;

    use Zf2Mvc\Controller\ActionController;

    class FooController extends ActionController
    {
        public function barAction()
        {
            // do some stuff
        }
    }

It is up to you as a developer what you will return from an action method. We
suggest returning either an associative array, or a value object of some kind.
This can then easily be passed to some sort of renderer during the execution
chain in order to generate a response payload.

Alternately, you could return a response object from your method. This allows
you to essentially return as early as possible from execution.

h3. RestfulController

`Zf2Mvc\Controller\RestfulController` is a somewhat naive interpretation of the
REST paradigm. It operates primarily as a CRUD (Create-Read-Update-Delete)-style
controller, but uses HTTP verbs in order to determine what to do.

*   GET can return either a list of resources, or a single resource.
    * With no identifier provided, a list of resources is returned.
    * With an identifier provided -- perhaps by the query string, potentially
        via routing -- a single resource is returned.
*   POST creates a new resource. Ideally, you will return it, as well as a
    Location header indicating the new URI to the resource, and a 201 HTTP 
    response code.
*   PUT requires an identifier, and uses the data provided to update the
    specified resource, assuming it exists.
*   DELETE requires an identifier, and should delete the given resource; 
    typically, a 204 response code should be returned.

Additionally, you can define "action" methods like you would with a standard
`ActionController`. If an "action" token is found in the route match, it will
look for a matching action method and execute it, or a "not-found" action if 
no match exists.
