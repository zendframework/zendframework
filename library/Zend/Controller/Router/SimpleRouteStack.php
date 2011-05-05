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
     * Create a new route stack
     *
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
     * @return Router
     */
    public function setOptions($options)
    {
        if ($options instanceof \Zend\Config) {
            $options = $options->toArray();
        }

        if (!is_array($options)) {
            throw new InvalidArgumentException('Options must either be an array or an instance of \Zend\Config');
        }

        foreach ($options as $key => $value) {
            switch ($key) {
                case 'routes':
                    $this->addRoutes($value);
                    break;
            }
        }
    }

    /**
     * Append multiple routes.
     *
     * @param  array $routes
     * @return Router
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }

        return $this;
    }

    /**
     * Append a route to the end of the list.
     *
     * @param  string  $name
     * @param  mixed   $route
     * @param  integer $priority
     * @return Router
     */
    public function addRoute($name, $route, $priority = null)
    {
        if (is_array($route)) {
            $route = $this->routeFromArray($specs);
        }

        if (!$route instanceof Route) {
            throw new InvalidArgumentException('Supplied route must either be an array or a Route object');
        }

        $this->routes->insert($name, $route, $priority);

        return $this;
    }

    /**
     * Create a route from array specifications.
     *
     * @param  array $specs
     * @return Route
     */
    protected function routeFromArray(array $specs)
    {
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
