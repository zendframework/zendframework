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

use ArrayAccess,
    Config,
    Traversable,
    Zend\Controller\Request,
    Zend\Controller\Router\Exception,
    Zend\Loader\PluginBroker;

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
        $this->routes = new PriorityList();

        if ($options !== null) {
            $this->setOptions($options);
        }
        
        if ($this->pluginBroker === null) {
            $this->pluginBroker = new PluginBroker(array(
                'auto_register_plugins' => false
            ));            
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
        if ($options instanceof Config) {
            $options = $options->toArray();
        } elseif ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array or Traversable object; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'routes':
                    $this->addRoutes($value);
                    break;
                
                case 'plugin_broker':
                    $this->setPluginBroker($value);
                    break;
                
                default:
                    break;
            }
        }
    }
    
    /**
     * Set the plugin broker.
     * 
     * @param  PluginBroker $broker
     * @return SimpleRouteStack
     */
    public function setPluginBroker(PluginBroker $broker)
    {
        $this->pluginBroker = $broker;
        return $this;
    }
    
    /**
     * Get the plugin broker.
     * 
     * @return PluginBroker
     */
    public function getPluginBroker()
    {
        return $this->pluginBroker;
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
        } elseif (!$routes instanceof Traversable) {
            throw new Exception\InvalidArgumentException('Routes provided are invalid; must be traversable');
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
        if (!$route instanceof Route) {
            $route = $this->routeFromArray($route);
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
     * @param  mixed $specs
     * @return SimpleRouteStack
     */
    protected function routeFromArray($specs)
    {
        if (!is_array($options) && !$options instanceof ArrayAccess) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array or ArrayAccess; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
    
        if (!isset($specs['type'])) {
            throw new Exception\InvalidArgumentException('Missing "type" option');
        } elseif (!isset($specs['options'])) {
            throw new Exception\InvalidArgumentException('Missing "name" option');
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
            throw new Exception\InvalidArgumentException('Missing "name" option');
        } elseif (null === ($route = $this->route->get($options['name']))) {
            throw new Exception\RuntimeException(sprintf('Route with name "%s" not found', $options['name']));
        }
        
        unset($options['name']);
        
        return $route->assemble($params, $options);
    }
}
