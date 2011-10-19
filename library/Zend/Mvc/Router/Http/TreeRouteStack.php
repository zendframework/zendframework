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
 * @subpackage Http
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router\Http;

use Zend\Mvc\Router\Exception,
    Zend\Mvc\Router\SimpleRouteStack,
    Zend\Mvc\Router\Route,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Uri\Http as HttpUri;

/**
 * Tree search implementation.
 *
 * @package    Zend_Mvc_Router
 * @subpackage Http
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
     * Request URI.
     *
     * @var HttpUri
     */
    protected $requestUri;

    /**
     * init(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::init()
     * @return void
     */
    protected function init()
    {
        $this->routeBroker->getClassLoader()->registerPlugins(array(
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
     * @param  array|Traversable $specs
     * @return Route
     */
    protected function routeFromArray($specs)
    {
        $route = parent::routeFromArray($specs);

        if (!$route instanceof Route) {
            throw new Exception\RuntimeException('Given route does not implement HTTP route interface');
        }

        if (isset($specs['child_routes'])) {
            $options = array(
                'route'         => $route,
                'may_terminate' => (isset($specs['may_terminate']) && $specs['may_terminate']),
                'child_routes'  => $specs['child_routes'],
                'route_broker'  => $this->routeBroker,
            );

            $route = $this->routeBroker->load('part', $options);
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

        $uri           = $request->uri();
        $baseUrlLength = strlen($this->baseUrl) ?: null;

        if ($this->requestUri === null) {
            $this->setRequestUri($uri);
        }

        if ($baseUrlLength !== null) {
            $pathLength = strlen($uri->getPath()) - $baseUrlLength;

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
     * assemble(): defined by Route interface.
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

        $names = explode('/', $options['name'], 2);
        $route = $this->routes->get($names[0]);

        if (!$route) {
            throw new Exception\RuntimeException(sprintf('Route with name "%s" not found', $names[0]));
        }

        if (isset($names[1])) {
            $options['name'] = $names[1];
        } else {
            unset($options['name']);
        }

        if (!isset($options['uri'])) {
            $uri = new HttpUri();

            if (isset($options['absolute']) && $options['absolute']) {
                if ($this->requestUri === null) {
                    throw new Exception\RuntimeException('Request URI has not been set');
                }

                $uri->setScheme($this->requestUri->getScheme())
                    ->setHost($this->requestUri->getHost())
                    ->setPort($this->requestUri->getPort());
            }

            $options['uri'] = $uri;
        }

        $path = $this->baseUrl . $route->assemble($params, $options);

        if (isset($uri)) {
            if (isset($options['absolute']) && $options['absolute']) {
                return $uri->setPath($path)->toString();
            } elseif ($uri->getHost() !== null) {
                if ($uri->scheme !== null) {
                    if ($this->requestUri === null) {
                        throw new Exception\RuntimeException('Request URI has not been set');
                    }

                    $uri->setScheme($this->requestUri->getScheme());
                }

                return $uri->setPath($path)->toString();
            }
        }

        return $path;
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
        return $this->baseUrl;
    }

    /**
     * Set the request URI.
     *
     * @param  HttpUri $uri
     * @return self
     */
    public function setRequestUri(HttpUri $uri)
    {
        $this->requestUri = $uri;
        return $this;
    }

    /**
     * Get the request URI.
     *
     * @return HttpUri
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }
}
