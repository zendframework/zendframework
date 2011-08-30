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
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Router;
use Zend\Config;

/**
 * Ruby routing based Router.
 *
 * @uses       \Zend\Controller\Router\AbstractRouter
 * @uses       \Zend\Controller\Router\Route
 * @uses       Zend\Loader
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Rewrite extends AbstractRouter
{

    /**
     * Whether or not to use default routes
     *
     * @var boolean
     */
    protected $_useDefaultRoutes = true;

    /**
     * Array of routes to match against
     *
     * @var array
     */
    protected $_routes = array();

    /**
     * Currently matched route
     *
     * @var \Zend\Controller\Router\Route
     */
    protected $_currentRoute = null;

    /**
     * Global parameters given to all routes
     *
     * @var array
     */
    protected $_globalParams = array();

    /**
     * Separator to use with chain names
     *
     * @var string
     */
    protected $_chainNameSeparator = '-';

    /**
     * Determines if request parameters should be used as global parameters
     * inside this router.
     *
     * @var boolean
     */
    protected $_useCurrentParamsAsGlobal = false;

    /**
     * Add default routes which are used to mimic basic router behaviour
     *
     * @return \Zend\Controller\Router\Rewrite
     */
    public function addDefaultRoutes()
    {
        if (!$this->hasRoute('application')) {
            $dispatcher = $this->getFrontController()->getDispatcher();
            $request = $this->getFrontController()->getRequest();

            require_once 'Zend/Controller/Router/Route/Module.php';
            $compat = new Route\Module(array(), $dispatcher, $request);

            $this->_routes = array_merge(array('application' => $compat), $this->_routes);
        }

        return $this;
    }

    /**
     * Add route to the route chain
     *
     * If route contains method setRequest(), it is initialized with a request object
     *
     * @param  string                                 $name       Name of the route
     * @param  \Zend\Controller\Router\Route $route      Instance of the route
     * @return \Zend\Controller\Router\Rewrite
     */
    public function addRoute($name, Route $route)
    {
        if (method_exists($route, 'setRequest')) {
            $route->setRequest($this->getFrontController()->getRequest());
        }

        $this->_routes[$name] = $route;

        return $this;
    }

    /**
     * Add routes to the route chain
     *
     * @param  array $routes Array of routes with names as keys and routes as values
     * @return \Zend\Controller\Router\Rewrite
     */
    public function addRoutes($routes) {
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }

        return $this;
    }

    /**
     * Create routes out of Zend_Config configuration
     *
     * Example INI:
     * routes.archive.route = "archive/:year/*"
     * routes.archive.defaults.controller = archive
     * routes.archive.defaults.action = show
     * routes.archive.defaults.year = 2000
     * routes.archive.reqs.year = "\d+"
     *
     * routes.news.type = "Zend_Controller_Router_Route_Static"
     * routes.news.route = "news"
     * routes.news.defaults.controller = "news"
     * routes.news.defaults.action = "list"
     *
     * And finally after you have created a Zend_Config with above ini:
     * $router = new Zend_Controller_Router_Rewrite();
     * $router->addConfig($config, 'routes');
     *
     * @param  \Zend\Config\Config $config  Configuration object
     * @param  string      $section Name of the config section containing route's definitions
     * @throws \Zend\Controller\Router\Exception
     * @return \Zend\Controller\Router\Rewrite
     */
    public function addConfig(Config\Config $config, $section = null)
    {
        if ($section !== null) {
            if ($config->{$section} === null) {
                require_once 'Zend/Controller/Router/Exception.php';
                throw new Exception("No route configuration in section '{$section}'");
            }

            $config = $config->{$section};
        }

        foreach ($config as $name => $info) {
            $route = $this->_getRouteFromConfig($info);

            if ($route instanceof Route\Chain) {
                if (!isset($info->chain)) {
                    require_once 'Zend/Controller/Router/Exception.php';
                    throw new Exception("No chain defined");
                }

                if ($info->chain instanceof Config\Config) {
                    $childRouteNames = $info->chain;
                } else {
                    $childRouteNames = explode(',', $info->chain);
                }

                foreach ($childRouteNames as $childRouteName) {
                    $childRoute = $this->getRoute(trim($childRouteName));
                    $route->addChain($childRoute);
                }

                $this->addRoute($name, $route);
            } elseif (isset($info->chains) && $info->chains instanceof Config\Config) {
                $this->_addChainRoutesFromConfig($name, $route, $info->chains);
            } else {
                $this->addRoute($name, $route);
            }
        }

        return $this;
    }

    /**
     * Get a route frm a config instance
     *
     * @param  \Zend\Config\Config $info
     * @return \Zend\Controller\Router\Route
     */
    protected function _getRouteFromConfig(Config\Config $info)
    {
        $class = (isset($info->type)) ? $info->type : 'Zend\Controller\Router\Route\Route';
        if (!class_exists($class, true)) {
            throw new \InvalidArgumentException('Class name ' . $class . ' does not exist.');
        }

        $route = $class::getInstance($info);

        if (isset($info->abstract) && $info->abstract && method_exists($route, 'isAbstract')) {
            $route->isAbstract(true);
        }

        return $route;
    }

    /**
     * Add chain routes from a config route
     *
     * @param  string                                 $name
     * @param  \Zend\Controller\Router\Route $route
     * @param  \Zend\Config\Config                            $childRoutesInfo
     * @return void
     */
    protected function _addChainRoutesFromConfig($name,
                                                 Route $route,
                                                 Config\Config $childRoutesInfo)
    {
        foreach ($childRoutesInfo as $childRouteName => $childRouteInfo) {
            if (is_string($childRouteInfo)) {
                $childRouteName = $childRouteInfo;
                $childRoute     = $this->getRoute($childRouteName);
            } else {
                $childRoute = $this->_getRouteFromConfig($childRouteInfo);
            }

            if ($route instanceof Route\Chain) {
                $chainRoute = clone $route;
                $chainRoute->addChain($childRoute);
            } else {
                $chainRoute = $route->addChain($childRoute);
            }

            $chainName = $name . $this->_chainNameSeparator . $childRouteName;

            if (isset($childRouteInfo->chains)) {
                $this->_addChainRoutesFromConfig($chainName, $chainRoute, $childRouteInfo->chains);
            } else {
                $this->addRoute($chainName, $chainRoute);
            }
        }
    }

    /**
     * Remove a route from the route chain
     *
     * @param  string $name Name of the route
     * @throws \Zend\Controller\Router\Exception
     * @return \Zend\Controller\Router\Rewrite
     */
    public function removeRoute($name)
    {
        if (!isset($this->_routes[$name])) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Exception("Route $name is not defined");
        }

        unset($this->_routes[$name]);

        return $this;
    }

    /**
     * Remove all standard default routes
     *
     * @param  \Zend\Controller\Router\Route Route
     * @return \Zend\Controller\Router\Rewrite
     */
    public function removeDefaultRoutes()
    {
        $this->_useDefaultRoutes = false;

        return $this;
    }

    /**
     * Check if named route exists
     *
     * @param  string $name Name of the route
     * @return boolean
     */
    public function hasRoute($name)
    {
        return isset($this->_routes[$name]);
    }

    /**
     * Retrieve a named route
     *
     * @param string $name Name of the route
     * @throws \Zend\Controller\Router\Exception
     * @return \Zend\Controller\Router\Route Route object
     */
    public function getRoute($name)
    {
        if (!isset($this->_routes[$name])) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Exception("Route $name is not defined");
        }

        return $this->_routes[$name];
    }

    /**
     * Retrieve a currently matched route
     *
     * @throws \Zend\Controller\Router\Exception
     * @return \Zend\Controller\Router\Route Route object
     */
    public function getCurrentRoute()
    {
        if (!isset($this->_currentRoute)) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Exception("Current route is not defined");
        }
        return $this->getRoute($this->_currentRoute);
    }

    /**
     * Retrieve a name of currently matched route
     *
     * @throws \Zend\Controller\Router\Exception
     * @return \Zend\Controller\Router\Route Route object
     */
    public function getCurrentRouteName()
    {
        if (!isset($this->_currentRoute)) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Exception("Current route is not defined");
        }
        return $this->_currentRoute;
    }

    /**
     * Retrieve an array of routes added to the route chain
     *
     * @return array All of the defined routes
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * Find a matching route to the current PATH_INFO and inject
     * returning values to the Request object.
     *
     * @throws \Zend\Controller\Router\Exception
     * @return \Zend\Controller\Request\AbstractRequest Request object
     */
    public function route(\Zend\Controller\Request\AbstractRequest $request)
    {
        if (!$request instanceof \Zend\Controller\Request\Http) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Exception('Zend_Controller_Router_Rewrite requires a Zend_Controller_Request_HTTP-based request object');
        }

        if ($this->_useDefaultRoutes) {
            $this->addDefaultRoutes();
        }

        // Find the matching route
        $routeMatched = false;
        
        foreach (array_reverse($this->_routes) as $name => $route) {
            // TODO: Should be an interface method. Hack for 1.0 BC
            if (method_exists($route, 'isAbstract') && $route->isAbstract()) {
                continue;
            }

            // TODO: Should be an interface method. Hack for 1.0 BC
            if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
                $match = $request->getPathInfo();
            } else {
                $match = $request;
            }

            if ($params = $route->match($match)) {
                $this->_setRequestParams($request, $params);
                $this->_currentRoute = $name;
                $routeMatched        = true;
                break;
            }
        }

         if (!$routeMatched) {
             require_once 'Zend/Controller/Router/Exception.php';
             throw new Exception('No route matched the request', 404);
         }

        if($this->_useCurrentParamsAsGlobal) {
            $params = $request->getParams();
            foreach($params as $param => $value) {
                $this->setGlobalParam($param, $value);
            }
        }

        return $request;

    }

    protected function _setRequestParams($request, $params)
    {
        foreach ($params as $param => $value) {

            $request->setParam($param, $value);

            if ($param === $request->getModuleKey()) {
                $request->setModuleName($value);
            }
            if ($param === $request->getControllerKey()) {
                $request->setControllerName($value);
            }
            if ($param === $request->getActionKey()) {
                $request->setActionName($value);
            }

        }
    }

    /**
     * Generates a URL path that can be used in URL creation, redirection, etc.
     *
     * @param  array $userParams Options passed by a user used to override parameters
     * @param  mixed $name The name of a Route to use
     * @param  bool $reset Whether to reset to the route defaults ignoring URL params
     * @param  bool $encode Tells to encode URL parts on output
     * @throws \Zend\Controller\Router\Exception
     * @return string Resulting absolute URL path
     */
    public function assemble($userParams, $name = null, $reset = false, $encode = true)
    {
        if ($name == null) {
            try {
                $name = $this->getCurrentRouteName();
            } catch (Exception $e) {
                $name = 'application';
            }
        }

        // Use UNION (+) in order to preserve numeric keys 
        $params = $userParams + $this->_globalParams;

        $route = $this->getRoute($name);
        $url   = $route->assemble($params, $reset, $encode);

        if (!preg_match('|^[a-z]+://|', $url)) {
            $url = rtrim($this->getFrontController()->getBaseUrl(), '/') . '/' . $url;
        }

        return $url;
    }

    /**
     * Set a global parameter
     *
     * @param  string $name
     * @param  mixed $value
     * @return \Zend\Controller\Router\Rewrite
     */
    public function setGlobalParam($name, $value)
    {
        $this->_globalParams[$name] = $value;

        return $this;
    }

    /**
     * Set the separator to use with chain names
     *
     * @param string $separator The separator to use
     * @return \Zend\Controller\Router\Rewrite
     */
    public function setChainNameSeparator($separator) {
        $this->_chainNameSeparator = $separator;

        return $this;
    }

    /**
     * Get the separator to use for chain names
     *
     * @return string
     */
    public function getChainNameSeparator() {
        return $this->_chainNameSeparator;
    }

    /**
     * Determines/returns whether to use the request parameters as global parameters.
     *
     * @param boolean|null $use
     *           Null/unset when you want to retrieve the current state.
     *           True when request parameters should be global, false otherwise
     * @return boolean|\Zend\Controller\Router\Rewrite
     *              Returns a boolean if first param isn't set, returns an
     *              instance of Zend_Controller_Router_Rewrite otherwise.
     *
     */
    public function useRequestParametersAsGlobal($use = null) {
        if($use === null) {
            return $this->_useCurrentParamsAsGlobal;
        }

        $this->_useCurrentParamsAsGlobal = (bool) $use;

        return $this;
    }
}
