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
 * @package    Zend_Mvc_Router
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Router;

use ArrayAccess,
    ArrayIterator,
    Traversable,
    Zend\Stdlib\ArrayUtils,
    Zend\Stdlib\RequestInterface as Request;

/**
 * Simple route stack implementation.
 *
 * @package    Zend_Mvc_Router
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SimpleRouteStack implements RouteStackInterface
{
    /**
     * Stack containing all routes.
     *
     * @var PriorityList
     */
    protected $routes;

    /**
     * Plugin broker to load routes.
     *
     * @var RouteBroker
     */
    protected $routeBroker;

    /**
     * Default parameters.
     *
     * @var array
     */
    protected $defaultParams = array();

    /**
     * Create a new simple route stack.
     */
    public function __construct()
    {
        $this->routes      = new PriorityList();
        $this->routeBroker = new RouteBroker();

        $this->init();
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    Route::factory()
     * @param  array|\Traversable $options
     * @return SimpleRouteStack
     */
    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        $instance = new static();

        if (isset($options['route_broker'])) {
            $instance->setRouteBroker($options['route_broker']);
        }

        if (isset($options['routes'])) {
            $instance->addRoutes($options['routes']);
        }

        if (isset($options['default_params'])) {
            $instance->setDefaultParams($options['default_params']);
        }

        return $instance;
    }

    /**
     * Init method for extending classes.
     *
     * @return void
     */
    protected function init()
    {
    }

    /**
     * Set the route broker.
     *
     * @param  RouteBroker $broker
     * @return SimpleRouteStack
     */
    public function setRouteBroker(RouteBroker $broker)
    {
        $this->routeBroker = $broker;
        return $this;
    }

    /**
     * Get the route broker.
     *
     * @return RouteBroker
     */
    public function routeBroker()
    {
        return $this->routeBroker;
    }

    /**
     * addRoutes(): defined by RouteStackInterface interface.
     *
     * @see    RouteStack::addRoutes()
     * @param  array|\Traversable $routes
     * @return SimpleRouteStack
     */
    public function addRoutes($routes)
    {
        if (!is_array($routes) && !$routes instanceof Traversable) {
            throw new Exception\InvalidArgumentException('addRoutes expects an array or Traversable set of routes');
        }

        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }

        return $this;
    }

    /**
     * addRoute(): defined by RouteStackInterface interface.
     *
     * @see    RouteStack::addRoute()
     * @param  string  $name
     * @param  mixed   $route
     * @param  integer $priority
     * @return SimpleRouteStack
     */
    public function addRoute($name, $route, $priority = null)
    {
        if (!$route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        if ($priority === null && isset($route->priority)) {
            $priority = $route->priority;
        }

        $this->routes->insert($name, $route, $priority);

        return $this;
    }

    /**
     * removeRoute(): defined by RouteStackInterface interface.
     *
     * @see    RouteStack::removeRoute()
     * @param  string  $name
     * @return SimpleRouteStack
     */
    public function removeRoute($name)
    {
        $this->routes->remove($name);
        return $this;
    }


    /**
     * setRoutes(): defined by RouteStackInterface interface.
     *
     * @param  array|\Traversable $routes
     * @return SimpleRouteStack
     */
    public function setRoutes($routes)
    {
        $this->routes->clear();
        $this->addRoutes($routes);
        return $this;
    }

    /**
     * Set a default parameters.
     *
     * @param  array $params
     * @return SimpleRouteStack
     */
    public function setDefaultParams(array $params)
    {
        $this->defaultParams = $params;
        return $this;
    }

    /**
     * Set a default parameter.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return SimpleRouteStack
     */
    public function setDefaultParam($name, $value)
    {
        $this->defaultParams[$name] = $value;
        return $this;
    }

    /**
     * Create a route from array specifications.
     *
     * @param  array|\Traversable $specs
     * @return SimpleRouteStack
     */
    protected function routeFromArray($specs)
    {
        if ($specs instanceof Traversable) {
            $specs = ArrayUtils::iteratorToArray($specs);
        } elseif (!is_array($specs)) {
            throw new Exception\InvalidArgumentException('Route definition must be an array or Traversable object');
        }

        if (!isset($specs['type'])) {
            throw new Exception\InvalidArgumentException('Missing "type" option');
        } elseif (!isset($specs['options'])) {
            $specs['options'] = array();
        }

        $route = $this->routeBroker()->load($specs['type'], $specs['options']);

        if (isset($specs['priority'])) {
            $route->priority = $specs['priority'];
        }

        return $route;
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    Route::match()
     * @param  Request $request
     * @return RouteMatch|null
     */
    public function match(Request $request)
    {
        foreach ($this->routes as $name => $route) {
            if (($match = $route->match($request)) instanceof RouteMatch) {
                $match->setMatchedRouteName($name);

                foreach ($this->defaultParams as $name => $value) {
                    if ($match->getParam($name) === null) {
                        $match->setParam($name, $value);
                    }
                }

                return $match;
            }
        }

        return null;
    }

    /**
     * assemble(): defined by RouteInterface interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        if (!isset($options['name'])) {
            throw new Exception\InvalidArgumentException('Missing "name" option');
        }

        $route = $this->routes->get($options['name']);

        if (!$route) {
            throw new Exception\RuntimeException(sprintf('Route with name "%s" not found', $options['name']));
        }

        unset($options['name']);

        return $route->assemble(array_merge($this->defaultParams, $params), $options);
    }
}
