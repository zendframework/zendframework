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
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Router\Rewrite;
use Zend\Controller\Router\Rewrite\Route;
use Zend\Controller\Request\Http as HttpRequest;

/**
 * Ruby routing based router
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Router
{
    /**
     * Heap containing all routes
     *
     * @var PriorityList
     */
    protected $_routes;

    /**
     * Instantiate a new router
     *
     * @param  mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->_routes = new PriorityList();

        if ($options !== null) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options of the router
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
     * Append multiple routes
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
     * Append a route to the end of the list
     *
     * @param  string $name
     * @param  mixed  $route
     * @return Router
     */
    public function addRoute($name, $route)
    {
        if (is_array($route)) {
            $route = $this->_routeFromArray($specs);
        }

        if (!$route instanceof Route\Route) {
            throw new InvalidArgumentException('Supplied route must either be an array or a route object');
        }

        $this->_routes[$name] = $route;

        return $this;
    }

    /**
     * Create a route from array specifications
     *
     * @param  array $specs
     * @return Route\Route
     */
    protected function _routeFromArray(array $specs)
    {
        if (!isset($specs['type']) || !is_string($specs['type'])) {
            throw new InvalidArgumentException('Type not defined or not a string');
        } elseif (!isset($specs['options']) || !is_array($specs['options'])) {
            throw new InvalidArgumentException('Options not defined or not an array');
        }

        if (strpos($specs['type'], '\\') !== false) {
            $className = $specs['type'];
        } else {
            $className = 'Route\\' . $specs['type'];
        }

        $route = new $className($specs['options']);

        if (isset($specs['routes'])) {
            $options = array(
                'route'         => $route,
                'may_terminate' => (isset($specs['may_terminate']) && $specs['may_terminate'])
            );

            $route = new Route\Part($options);

            foreach ($specs['routes'] as $subName => $subSpecs) {
                $route->append($subName, $this->_routeFromArray($subSpecs));
            }
        }

        return $route;
    }

    /**
     * Match a request
     *
     * @param  HttpRequest $request
     * @return RouterMatch
     */
    public function match(HttpRequest $request)
    {
        $match = null;

        foreach ($this->_routes as $route) {
            if (($result = $route->match($request)) !== null) {
            }
        }

        return $match;
    }

    /**
     * Assemble an URL
     *
     * @param  array $params
     * @param  array $options
     * @return string
     */
    public function assemble(array $params = null, array $options = null)
    {
        
    }
}
