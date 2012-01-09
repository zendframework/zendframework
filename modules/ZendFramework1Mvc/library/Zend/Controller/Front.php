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
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller;
use Zend,
    Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Front
{
    /**
     * Base URL
     * @var string
     */
    protected $_baseUrl = null;

    /**
     * Directory|ies where controllers are stored
     *
     * @var string|array
     */
    protected $_controllerDir = null;

    /**
     * Instance of Zend\Controller\Dispatcher
     * @var \Zend\Controller\Dispatcher
     */
    protected $_dispatcher = null;

    /**
     * Helper broker to inject into action helpers
     * @var Zend\Controller\Action\HelperBroker
     */
    protected $helperBroker = null;

    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var \Zend\Controller\Front
     */
    protected static $_instance = null;

    /**
     * Array of invocation parameters to use when instantiating action
     * controllers
     * @var array
     */
    protected $_invokeParams = array();

    /**
     * Subdirectory within a module containing controllers; defaults to 'controllers'
     * @var string
     */
    protected $_moduleControllerDirectoryName = 'controllers';

    /**
     * Instance of Zend_Controller_Plugin_Broker
     * @var \Zend\Controller\Plugin\Broker
     */
    protected $_plugins = null;

    /**
     * Instance of Zend_Controller_Request_Abstract
     * @var \Zend\Controller\Request\AbstractRequest
     */
    protected $_request = null;

    /**
     * Instance of Zend_Controller_Response_Abstract
     * @var \Zend\Controller\Response\AbstractResponse
     */
    protected $_response = null;

    /**
     * Whether or not to return the response prior to rendering output while in
     * {@link dispatch()}; default is to send headers and render output.
     * @var boolean
     */
    protected $_returnResponse = false;

    /**
     * Instance of Zend\Controller\Router
     * @var \Zend\Controller\Router
     */
    protected $_router = null;

    /**
     * Whether or not exceptions encountered in {@link dispatch()} should be
     * thrown or trapped in the response object
     * @var boolean
     */
    protected $_throwExceptions = false;

    /**
     * Constructor
     *
     * Instantiate using {@link getInstance()}; front controller is a singleton
     * object.
     *
     * Instantiates the plugin broker.
     *
     * @return void
     */
    protected function __construct()
    {
        $this->_plugins = new Plugin\Broker();
    }

    /**
     * Enforce singleton; disallow cloning
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Singleton instance
     *
     * @return \Zend\Controller\Front
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Resets all object properties of the singleton instance
     *
     * Primarily used for testing; could be used to chain front controllers.
     *
     * Also resets action helper broker, clearing all registered helpers.
     *
     * @return void
     */
    public function resetInstance()
    {
        $reflection = new \ReflectionObject($this);
        foreach ($reflection->getProperties() as $property) {
            $name = $property->getName();
            switch ($name) {
                case '_instance':
                    break;
                case '_controllerDir':
                case '_invokeParams':
                    $this->{$name} = array();
                    break;
                case '_plugins':
                    $this->{$name} = new Plugin\Broker();
                    break;
                case '_throwExceptions':
                case '_returnResponse':
                    $this->{$name} = false;
                    break;
                case '_moduleControllerDirectoryName':
                    $this->{$name} = 'controllers';
                    break;
                default:
                    $this->{$name} = null;
                    break;
            }
        }
    }

    /**
     * Convenience feature, calls setControllerDirectory()->setRouter()->dispatch()
     *
     * In PHP 5.1.x, a call to a static method never populates $this -- so run()
     * may actually be called after setting up your front controller.
     *
     * @param string|array $controllerDirectory Path to \Zend\Controller\Action\Action
     * controller classes or array of such paths
     * @return void
     * @throws \Zend\Controller\Exception if called from an object instance
     */
    public static function run($controllerDirectory)
    {
        self::getInstance()
            ->setControllerDirectory($controllerDirectory)
            ->dispatch();
    }

    /**
     * Add a controller directory to the controller directory stack
     *
     * If $args is presented and is a string, uses it for the array key mapping
     * to the directory specified.
     *
     * @param string $directory
     * @param string $module Optional argument; module with which to associate directory. If none provided, assumes 'default'
     * @return \Zend\Controller\Front
     * @throws \Zend\Controller\Exception if directory not found or readable
     */
    public function addControllerDirectory($directory, $module = null)
    {
        $this->getDispatcher()->addControllerDirectory($directory, $module);
        return $this;
    }

    /**
     * Set controller directory
     *
     * Stores controller directory(ies) in dispatcher. May be an array of
     * directories or a string containing a single directory.
     *
     * @param string|array $directory Path to \Zend\Controller\Action\Action controller
     * classes or array of such paths
     * @param  string $module Optional module name to use with string $directory
     * @return \Zend\Controller\Front
     */
    public function setControllerDirectory($directory, $module = null)
    {
        $this->getDispatcher()->setControllerDirectory($directory, $module);
        return $this;
    }

    /**
     * Retrieve controller directory
     *
     * Retrieves:
     * - Array of all controller directories if no $name passed
     * - String path if $name passed and exists as a key in controller directory array
     * - null if $name passed but does not exist in controller directory keys
     *
     * @param  string $name Default null
     * @return array|string|null
     */
    public function getControllerDirectory($name = null)
    {
        return $this->getDispatcher()->getControllerDirectory($name);
    }

    /**
     * Remove a controller directory by module name
     *
     * @param  string $module
     * @return bool
     */
    public function removeControllerDirectory($module)
    {
        return $this->getDispatcher()->removeControllerDirectory($module);
    }

    /**
     * Specify a directory as containing modules
     *
     * Iterates through the directory, adding any subdirectories as modules;
     * the subdirectory within each module named after {@link $_moduleControllerDirectoryName}
     * will be used as the controller directory path.
     *
     * @param  string $path
     * @return \Zend\Controller\Front
     */
    public function addModuleDirectory($path)
    {
        try{
            $dir = new \DirectoryIterator($path);
        } catch(\Exception $e) {
            throw new Exception("Directory $path not readable", 0, $e);
        }
        foreach ($dir as $file) {
            if ($file->isDot() || !$file->isDir()) {
                continue;
            }

            $module    = $file->getFilename();

            // Don't use SCCS directories as modules
            if (preg_match('/^[^a-z]/i', $module) || ('CVS' == $module)) {
                continue;
            }

            $moduleDir = $file->getPathname() . DIRECTORY_SEPARATOR . $this->getModuleControllerDirectoryName();
            $this->addControllerDirectory($moduleDir, $module);
        }

        return $this;
    }

    /**
     * Return the path to a module directory (but not the controllers directory within)
     *
     * @param  string $module
     * @return string|null
     */
    public function getModuleDirectory($module = null)
    {
        if (null === $module) {
            $request = $this->getRequest();
            if (null !== $request) {
                $module = $this->getRequest()->getModuleName();
            }
            if (empty($module)) {
                $module = $this->getDispatcher()->getDefaultModule();
            }
        }

        $controllerDir = $this->getControllerDirectory($module);

        if ((null === $controllerDir) || !is_string($controllerDir)) {
            return null;
        }

        return dirname($controllerDir);
    }

    /**
     * Set the directory name within a module containing controllers
     *
     * @param  string $name
     * @return \Zend\Controller\Front
     */
    public function setModuleControllerDirectoryName($name = 'controllers')
    {
        $this->_moduleControllerDirectoryName = (string) $name;

        return $this;
    }

    /**
     * Return the directory name within a module containing controllers
     *
     * @return string
     */
    public function getModuleControllerDirectoryName()
    {
        return $this->_moduleControllerDirectoryName;
    }

    /**
     * Set the default controller (unformatted string)
     *
     * @param string $controller
     * @return \Zend\Controller\Front
     */
    public function setDefaultControllerName($controller)
    {
        $dispatcher = $this->getDispatcher();
        $dispatcher->setDefaultControllerName($controller);
        return $this;
    }

    /**
     * Retrieve the default controller (unformatted string)
     *
     * @return string
     */
    public function getDefaultControllerName()
    {
        return $this->getDispatcher()->getDefaultControllerName();
    }

    /**
     * Set the default action (unformatted string)
     *
     * @param string $action
     * @return \Zend\Controller\Front
     */
    public function setDefaultAction($action)
    {
        $dispatcher = $this->getDispatcher();
        $dispatcher->setDefaultAction($action);
        return $this;
    }

    /**
     * Retrieve the default action (unformatted string)
     *
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->getDispatcher()->getDefaultAction();
    }

    /**
     * Set the default module name
     *
     * @param string $module
     * @return \Zend\Controller\Front
     */
    public function setDefaultModule($module)
    {
        $dispatcher = $this->getDispatcher();
        $dispatcher->setDefaultModule($module);
        return $this;
    }

    /**
     * Retrieve the default module
     *
     * @return string
     */
    public function getDefaultModule()
    {
        return $this->getDispatcher()->getDefaultModule();
    }

    /**
     * Set request class/object
     *
     * Set the request object.  The request holds the request environment.
     *
     * If a class name is provided, it will instantiate it
     *
     * @param string|\Zend\Controller\Request\AbstractRequest $request
     * @throws \Zend\Controller\Exception if invalid request class
     * @return \Zend\Controller\Front
     */
    public function setRequest($request)
    {
        if (is_string($request)) {
            if (!class_exists($request)) {
                \Zend\Loader::loadClass($request);
            }
            $request = new $request();
        }
        if (!$request instanceof Request\AbstractRequest) {
            throw new Exception('Invalid request class');
        }

        $this->_request = $request;

        return $this;
    }

    /**
     * Return the request object.
     *
     * @return null|\Zend\Controller\Request\AbstractRequest
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set router class/object
     *
     * Set the router object.  The router is responsible for mapping
     * the request to a controller and action.
     *
     * If a class name is provided, instantiates router with any parameters
     * registered via {@link setParam()} or {@link setParams()}.
     *
     * @param string|\Zend\Controller\Router $router
     * @throws \Zend\Controller\Exception if invalid router class
     * @return \Zend\Controller\Front
     */
    public function setRouter($router)
    {
        if (is_string($router)) {
            if (!class_exists($router)) {
                \Zend\Loader::loadClass($router);
            }
            $router = new $router();
        }

        if (!$router instanceof Router) {
            throw new Exception('Invalid router class');
        }

        $router->setFrontController($this);
        $this->_router = $router;

        return $this;
    }

    /**
     * Return the router object.
     *
     * Instantiates a Zend_Controller_Router_Rewrite object if no router currently set.
     *
     * @return \Zend\Controller\Router
     */
    public function getRouter()
    {
        if (null == $this->_router) {
            $this->setRouter(new Router\Rewrite());
        }

        return $this->_router;
    }

    /**
     * Set the base URL used for requests
     *
     * Use to set the base URL segment of the REQUEST_URI to use when
     * determining PATH_INFO, etc. Examples:
     * - /admin
     * - /myapp
     * - /subdir/index.php
     *
     * Note that the URL should not include the full URI. Do not use:
     * - http://example.com/admin
     * - http://example.com/myapp
     * - http://example.com/subdir/index.php
     *
     * If a null value is passed, this can be used as well for autodiscovery (default).
     *
     * @param string $base
     * @return \Zend\Controller\Front
     * @throws \Zend\Controller\Exception for non-string $base
     */
    public function setBaseUrl($base = null)
    {
        if (!is_string($base) && (null !== $base)) {
            throw new Exception('Rewrite base must be a string');
        }

        $this->_baseUrl = $base;

        if ((null !== ($request = $this->getRequest())) && (method_exists($request, 'setBaseUrl'))) {
            $request->setBaseUrl($base);
        }

        return $this;
    }

    /**
     * Retrieve the currently set base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $request = $this->getRequest();
        if ((null !== $request) && method_exists($request, 'getBaseUrl')) {
            return $request->getBaseUrl();
        }

        return $this->_baseUrl;
    }

    /**
     * Set the dispatcher object.  The dispatcher is responsible for
     * taking a Zend_Controller_Dispatcher_Token object, instantiating the controller, and
     * call the action method of the controller.
     *
     * @param \Zend\Controller\Dispatcher $dispatcher
     * @return \Zend\Controller\Front
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Return the dispatcher object.
     *
     * @return \Zend\Controller\Dispatcher
     */
    public function getDispatcher()
    {
        /**
         * Instantiate the default dispatcher if one was not set.
         */
        if (!$this->_dispatcher instanceof Dispatcher) {
            $this->_dispatcher = new Dispatcher\Standard();
        }
        return $this->_dispatcher;
    }

    /**
     * Set response class/object
     *
     * Set the response object.  The response is a container for action
     * responses and headers. Usage is optional.
     *
     * If a class name is provided, instantiates a response object.
     *
     * @param string|\Zend\Controller\Response\AbstractResponse $response
     * @throws \Zend\Controller\Exception if invalid response class
     * @return \Zend\Controller\Front
     */
    public function setResponse($response)
    {
        if (is_string($response)) {
            if (!class_exists($response)) {
                \Zend\Loader::loadClass($response);
            }
            $response = new $response();
        }
        if (!$response instanceof Response\AbstractResponse) {
            throw new Exception('Invalid response class');
        }

        $this->_response = $response;

        return $this;
    }

    /**
     * Return the response object.
     *
     * @return null|\Zend\Controller\Response\AbstractResponse
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Add or modify a parameter to use when instantiating an action controller
     *
     * @param string $name
     * @param mixed $value
     * @return \Zend\Controller\Front
     */
    public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->_invokeParams[$name] = $value;
        return $this;
    }

    /**
     * Set parameters to pass to action controller constructors
     *
     * @param array $params
     * @return \Zend\Controller\Front
     */
    public function setParams(array $params)
    {
        $this->_invokeParams = array_merge($this->_invokeParams, $params);
        return $this;
    }

    /**
     * Retrieve a single parameter from the controller parameter stack
     *
     * @param string $name
     * @return mixed
     */
    public function getParam($name)
    {
        if(isset($this->_invokeParams[$name])) {
            return $this->_invokeParams[$name];
        }

        return null;
    }

    /**
     * Retrieve action controller instantiation parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_invokeParams;
    }

    /**
     * Clear the controller parameter stack
     *
     * By default, clears all parameters. If a parameter name is given, clears
     * only that parameter; if an array of parameter names is provided, clears
     * each.
     *
     * @param null|string|array single key or array of keys for params to clear
     * @return \Zend\Controller\Front
     */
    public function clearParams($name = null)
    {
        if (null === $name) {
            $this->_invokeParams = array();
        } elseif (is_string($name) && isset($this->_invokeParams[$name])) {
            unset($this->_invokeParams[$name]);
        } elseif (is_array($name)) {
            foreach ($name as $key) {
                if (is_string($key) && isset($this->_invokeParams[$key])) {
                    unset($this->_invokeParams[$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Register a plugin.
     *
     * @param  \Zend\Controller\Plugin\AbstractPlugin $plugin
     * @param  int $stackIndex Optional; stack index for plugin
     * @return \Zend\Controller\Front
     */
    public function registerPlugin(Plugin\AbstractPlugin $plugin, $stackIndex = null)
    {
        $this->_plugins->registerPlugin($plugin, $stackIndex);
        return $this;
    }

    /**
     * Unregister a plugin.
     *
     * @param  string|\Zend\Controller\Plugin\AbstractPlugin $plugin Plugin class or object to unregister
     * @return \Zend\Controller\Front
     */
    public function unregisterPlugin($plugin)
    {
        $this->_plugins->unregisterPlugin($plugin);
        return $this;
    }

    /**
     * Is a particular plugin registered?
     *
     * @param  string $class
     * @return bool
     */
    public function hasPlugin($class)
    {
        return $this->_plugins->hasPlugin($class);
    }

    /**
     * Retrieve a plugin or plugins by class
     *
     * @param  string $class
     * @return false|\Zend\Controller\Plugin\AbstractPlugin|array
     */
    public function getPlugin($class)
    {
        return $this->_plugins->getPlugin($class);
    }

    /**
     * Retrieve all plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->_plugins->getPlugins();
    }

    /**
     * Set helper broker to inject in action controllers
     * 
     * @param  string|Broker $broker 
     * @return Front
     */
    public function setHelperBroker($broker)
    {
        if (is_string($broker)) {
            if (!class_exists($broker)) {
                throw new Exception(sprintf(
                    'Could not find Action HelperBroker by name of "%s"',
                    $broker
                ));
            }
            $broker = new $broker();
        }
        if (!$broker instanceof Broker) {
            throw new Exception(sprintf(
                'HelperBroker must implement Broker; received "%s"',
                (is_object($broker) ? get_class($broker) : gettype($broker))
            ));
        }
        $this->helperBroker = $broker;
        return $this;
    }

    /**
     * Retrieve action helper broker
     * 
     * @return Broker
     */
    public function getHelperBroker()
    {
        if (null === $this->helperBroker) {
            $this->setHelperBroker(new Action\HelperBroker());
        }
        return $this->helperBroker;
    }

    /**
     * Set the throwExceptions flag and retrieve current status
     *
     * Set whether exceptions encounted in the dispatch loop should be thrown
     * or caught and trapped in the response object.
     *
     * Default behaviour is to trap them in the response object; call this
     * method to have them thrown.
     *
     * Passing no value will return the current value of the flag; passing a
     * boolean true or false value will set the flag and return the current
     * object instance.
     *
     * @param boolean $flag Defaults to null (return flag state)
     * @return boolean|\Zend\Controller\Front Used as a setter, returns object; as a getter, returns boolean
     */
    public function throwExceptions($flag = null)
    {
        if ($flag !== null) {
            $this->_throwExceptions = (bool) $flag;
            return $this;
        }

        return $this->_throwExceptions;
    }

    /**
     * Set whether {@link dispatch()} should return the response without first
     * rendering output. By default, output is rendered and dispatch() returns
     * nothing.
     *
     * @param boolean $flag
     * @return boolean|\Zend\Controller\Front Used as a setter, returns object; as a getter, returns boolean
     */
    public function returnResponse($flag = null)
    {
        if (true === $flag) {
            $this->_returnResponse = true;
            return $this;
        } elseif (false === $flag) {
            $this->_returnResponse = false;
            return $this;
        }

        return $this->_returnResponse;
    }

    /**
     * Dispatch an HTTP request to a controller/action.
     *
     * @param \Zend\Controller\Request\AbstractRequest|null $request
     * @param \Zend\Controller\Response\AbstractResponse|null $response
     * @return void|\Zend\Controller\Response\AbstractResponse Returns response object if returnResponse() is true
     */
    public function dispatch(Request\AbstractRequest $request = null, Response\AbstractResponse $response = null)
    {
        $helperBroker = $this->getHelperBroker();
        if (!$this->getParam('noErrorHandler') && !$this->_plugins->hasPlugin('\Zend\Controller\Plugin\ErrorHandler')) {
            // Register with stack index of 100
            $this->_plugins->registerPlugin(new Plugin\ErrorHandler(), 100);
        }

        if (!$this->getParam('noViewRenderer') && !$helperBroker->hasPlugin('viewRenderer')) {
            $viewRenderer = $helperBroker->load('viewrenderer');
            $helperBroker->getStack()->offsetSet(-80, $viewRenderer);
        }

        /**
         * Instantiate default request object (HTTP version) if none provided
         */
        if (null !== $request) {
            $this->setRequest($request);
        } elseif ((null === $request) && (null === ($request = $this->getRequest()))) {
            $request = new Request\Http();
            $this->setRequest($request);
        }

        /**
         * Set base URL of request object, if available
         */
        if (is_callable(array($this->_request, 'setBaseUrl'))) {
            if (null !== $this->_baseUrl) {
                $this->_request->setBaseUrl($this->_baseUrl);
            }
        }

        /**
         * Instantiate default response object (HTTP version) if none provided
         */
        if (null !== $response) {
            $this->setResponse($response);
        } elseif ((null === $this->_response) && (null === ($this->_response = $this->getResponse()))) {
            $response = new Response\Http();
            $this->setResponse($response);
        }

        /**
         * Register request and response objects with plugin broker
         */
        $this->_plugins
             ->setRequest($this->_request)
             ->setResponse($this->_response)
             ->setHelperBroker($helperBroker);

        /**
         * Initialize router
         */
        $router = $this->getRouter();
        $router->setParams($this->getParams());

        /**
         * Initialize dispatcher
         */
        $dispatcher = $this->getDispatcher();
        $dispatcher->setParams($this->getParams())
                   ->setResponse($this->_response)
                   ->setHelperBroker($helperBroker);

        // Begin dispatch
        try {
            /**
             * Route request to controller/action, if a router is provided
             */

            /**
            * Notify plugins of router startup
            */
            $this->_plugins->routeStartup($this->_request);

            try {
                $router->route($this->_request);
            }  catch (\Exception $e) {
                if ($this->throwExceptions()) {
                    throw $e;
                }

                $this->_response->setException($e);
            }

            /**
            * Notify plugins of router completion
            */
            $this->_plugins->routeShutdown($this->_request);

            /**
             * Notify plugins of dispatch loop startup
             */
            $this->_plugins->dispatchLoopStartup($this->_request);

            /**
             *  Attempt to dispatch the controller/action. If the $this->_request
             *  indicates that it needs to be dispatched, move to the next
             *  action in the request.
             */
            do {
                $this->_request->setDispatched(true);

                /**
                 * Notify plugins of dispatch startup
                 */
                $this->_plugins->preDispatch($this->_request);

                /**
                 * Skip requested action if preDispatch() has reset it
                 */
                if (!$this->_request->isDispatched()) {
                    continue;
                }

                /**
                 * Dispatch request
                 */
                try {
                    $dispatcher->dispatch($this->_request, $this->_response);
                } catch (\Exception $e) {
                    if ($this->throwExceptions()) {
                        throw $e;
                    }
                    $this->_response->setException($e);
                }

                /**
                 * Notify plugins of dispatch completion
                 */
                $this->_plugins->postDispatch($this->_request);
            } while (!$this->_request->isDispatched());
        } catch (\Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }

            $this->_response->setException($e);
        }

        /**
         * Notify plugins of dispatch loop completion
         */
        try {
            $this->_plugins->dispatchLoopShutdown();
        } catch (\Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }

            $this->_response->setException($e);
        }

        if ($this->returnResponse()) {
            return $this->_response;
        }

        $this->_response->sendResponse();
    }
}
