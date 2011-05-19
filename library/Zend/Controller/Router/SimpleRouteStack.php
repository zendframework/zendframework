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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Router;
use Zend\Controller\Request;
use Zend\Loader\PluginBroker;

/**
 * Simple route stack implementation.
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SimpleRouteStack implements RouteStack
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
     * @var PluginBroker
     */
    protected $pluginBroker;

    /**
     * __construct(): defined by Route interface.
     *
     * @see    Route::__construct()
     * @param  mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->routes       = new PriorityList();
        $this->pluginBroker = new PluginBroker(array(
            'auto_register_plugins' => false
        ));

        if ($options !== null) {
            $this->setOptions($options);
        }
        
        $this->init();
    }
    
    /**
     * Init method for extending classes.
     * 
     * @return void
     */
    protected function init()
    {}

    /**
     * Set options of the route stack.
     *
     * @param  mixed $options
     * @return RouteStack
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof \Traversable) {
            throw new InvalidArgumentException(sprintf(
                'Expected an array or Traversable; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'routes':
                    $this->addRoutes($value);
                    break;
                
                default:
                    break;
            }
        }
    }

    /**
     * addRoutes(): defined by RouteStack interface.
     *
     * @see    Route::addRoutes()
     * @param  mixed $routes
     * @return RouteStack
     */
    public function addRoutes($routes)
    {
        if (is_array($routes)) {
            $routes = new ArrayIterator($routes);
        }
        
        if (!$routes instanceof Traversable) {
            throw new InvalidArgumentException('Routes provided are invalid; must be traversable');
        }        
        
        $routeStack = $this;
        
        iterator_apply($routes, function() use ($routeStack, $routes) {
            $routeStack->addRoute($routes->key(), $routes->current());
            return true;
        });

        return $this;
    }

    /**
     * addRoute(): defined by RouteStack interface.
     *
     * @see    Route::addRoute()
     * @param  string  $name
     * @param  mixed   $route
     * @param  integer $priority
     * @return RouteStack
     */
    public function addRoute($name, $route, $priority = null)
    {
        if (is_array($route) || $route instanceof \ArrayAccess) {
            $route = $this->routeFromArray($specs);
        }

        if (!$route instanceof Route) {
            throw new InvalidArgumentException('Supplied route must either be an array or a Route object');
        }

        $this->routes->insert($name, $route, $priority);

        return $this;
    }

    /**
     * removeRoute(): defined by RouteStack interface.
     *
     * @see    Route::removeRoute()
     * @param  string  $name
     * @return RouteStack
     */
    public function removeRoute($name)
    {
        $this->routes->remove($name);

        return $this;
    }

    /**
     * Create a route from array specifications.
     *
     * @param  array $specs
     * @return SimpleRouteStack
     */
    protected function routeFromArray(array $specs)
    {
        if (!is_array($options) && !$options instanceof \ArrayAccess) {
            throw new InvalidArgumentException(sprintf(
                'Expected an array or ArrayAccess; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
    
        if (!isset($specs['type']) || !is_string($specs['type'])) {
            throw new InvalidArgumentException('Type not defined or not a string');
        } elseif (!isset($specs['options']) || !is_array($specs['options'])) {
            throw new InvalidArgumentException('Options not defined or not an array');
        }
        
        $route = $this->pluginBroker->load($specs['type'], $specs['options']);

        return $route;
    }

    /**
     * match(): defined by Route interface.
     *
     * @see    Route::match()
     * @param  Request $request
     * @return RouteMatch
     */
    public function match(Request $request)
    {
        foreach ($this->routes as $route) {
            if (null !== ($result = $route->match($request))) {
                return $match;
            }
        }

        return null;
    }

    /**
     * assemble(): defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = null, array $options = null)
    {
        if (!isset($options['name'])) {
            throw new InvalidArgumentException('Name not defined');
        }
        
        if (null === ($route = $this->route->get($options['name']))) {
            throw new RuntimeException(sprintf('Route with name "%s" not found', $options['name']));
        }
        
        unset($options['name']);
        
        return $route->assemble($params, $options);
    }
}
