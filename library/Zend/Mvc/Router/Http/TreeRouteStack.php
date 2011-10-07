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
 * @package    Zend_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router\Http;

use Zend\Mvc\Router\Exception,
    Zend\Loader\PluginSpecBroker,
    Zend\Mvc\Router\SimpleRouteStack,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Mvc\Router\Route;

/**
 * Tree search implementation.
 *
 * @package    Zend_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TreeRouteStack extends SimpleRouteStack
{
    /**
     * Base URL.
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * init(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::init()
     * @return void
     */
    protected function init()
    {
        $this->pluginBroker->getClassLoader()->registerPlugins(array(
            'literal' => __NAMESPACE__ . '\Literal',
            'regex'   => __NAMESPACE__ . '\Regex',
            'segment' => __NAMESPACE__ . '\Segment',
            'part'    => __NAMESPACE__ . '\Part',
        ));
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
     * routeFromArray(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::routeFromArray()
     * @param  mixed $specs
     * @return Route
     */
    protected function routeFromArray($specs)
    {
        $route = parent::routeFromArray($specs);

        if (!$route instanceof Route) {
            throw new Exception\RuntimeException('Given route does not implement HTTP route interface');
        }

        if (isset($specs['routes'])) {
            $options = array(
                'route'         => $route,
                'may_terminate' => (isset($specs['may_terminate']) && $specs['may_terminate']),
                'child_routes'  => $specs['routes'],
                'plugin_broker' => $this->pluginBroker,
            );

            $route = $this->pluginBroker->load('part', $options);
        }

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
        if (!method_exists($request, 'uri')) {
            return null;
        }

        $baseUrlLength = strlen($this->baseUrl) ?: null;

        if ($baseUrlLength !== null) {
            $pathLength = strlen($request->uri()->getPath()) - $baseUrlLength;

            foreach ($this->routes as $route) {
                if (($match = $route->match($request, $baseUrlLength)) instanceof RouteMatch && $match->getLength() === $pathLength) {
                    return $match;
                }
            }
        } else {
            return parent::match($request);
        }

        return null;
    }

    /**
     * Set the base URL.
     *
     * @param  string $baseUrl
     * @return self
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl();
    }
}
