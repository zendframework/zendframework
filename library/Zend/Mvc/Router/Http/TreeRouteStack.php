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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Router\Http;

use Zend\Mvc\Router\Exception,
    Traversable,
    Zend\Stdlib\ArrayUtils,
    Zend\Mvc\Router\SimpleRouteStack,
    Zend\Mvc\Router\RouteInterface as BaseRoute,
    Zend\Mvc\Router\Http\RouteInterface,
    Zend\Stdlib\RequestInterface as Request,
    Zend\Uri\Http as HttpUri;

/**
 * Tree search implementation.
 *
 * @package    Zend_Mvc_Router
 * @subpackage Http
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TreeRouteStack extends SimpleRouteStack
{
    /**
     * Base URL.
     *
     * @var string
     */
    protected $baseUrl;

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
     */
    protected function init()
    {
        $this->routeBroker->getClassLoader()->registerPlugins(array(
            'hostname' => __NAMESPACE__ . '\Hostname',
            'literal'  => __NAMESPACE__ . '\Literal',
            'part'     => __NAMESPACE__ . '\Part',
            'regex'    => __NAMESPACE__ . '\Regex',
            'scheme'   => __NAMESPACE__ . '\Scheme',
            'segment'  => __NAMESPACE__ . '\Segment',
            'wildcard' => __NAMESPACE__ . '\Wildcard',
            'query'    => __NAMESPACE__ . '\Query',
            'method'   => __NAMESPACE__ . '\Method',
        ));
    }

    /**
     * addRoute(): defined by RouteStackInterface interface.
     *
     * @see    RouteStack::addRoute()
     * @param  string  $name
     * @param  mixed   $route
     * @param  integer $priority
     * @return TreeRouteStack
     */
    public function addRoute($name, $route, $priority = null)
    {
        if (!$route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        return parent::addRoute($name, $route, $priority);
    }

    /**
     * routeFromArray(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::routeFromArray()
     * @param  array|\Traversable $specs
     * @return RouteInterface
     */
    protected function routeFromArray($specs)
    {
        if ($specs instanceof Traversable) {
            $specs = ArrayUtils::iteratorToArray($specs);
        } elseif (!is_array($specs)) {
            throw new Exception\InvalidArgumentException('Route definition must be an array or Traversable object');
        }

        $route = parent::routeFromArray($specs);

        if (!$route instanceof RouteInterface) {
            throw new Exception\RuntimeException('Given route does not implement HTTP route interface');
        }

        if (isset($specs['child_routes'])) {
            $options = array(
                'route'         => $route,
                'may_terminate' => (isset($specs['may_terminate']) && $specs['may_terminate']),
                'child_routes'  => $specs['child_routes'],
                'route_broker'  => $this->routeBroker,
            );

            $priority = (isset($route->priority) ? $route->priority : null);

            $route = $this->routeBroker->load('part', $options);
            $route->priority = $priority;
        }

        return $route;
    }

    /**
     * match(): defined by BaseRoute interface.
     *
     * @see    BaseRoute::match()
     * @param  Request $request
     * @return RouteMatch
     */
    public function match(Request $request)
    {
        if (!method_exists($request, 'uri')) {
            return null;
        }

        if ($this->baseUrl === null && method_exists($request, 'getBaseUrl')) {
            $this->setBaseUrl($request->getBaseUrl());
        }

        $uri           = $request->uri();
        $baseUrlLength = strlen($this->baseUrl) ?: null;

        if ($this->requestUri === null) {
            $this->setRequestUri($uri);
        }

        if ($baseUrlLength !== null) {
            $pathLength = strlen($uri->getPath()) - $baseUrlLength;

            foreach ($this->routes as $name => $route) {
                if (($match = $route->match($request, $baseUrlLength)) instanceof RouteMatch && $match->getLength() === $pathLength) {
                    $match->setMatchedRouteName($name);

                    foreach ($this->defaultParams as $name => $value) {
                        if ($match->getParam($name) === null) {
                            $match->setParam($name, $value);
                        }
                    }

                    return $match;
                }
            }
        } else {
            return parent::match($request);
        }

        return null;
    }

    /**
     * assemble(): defined by RouteInterface interface.
     *
     * @see    BaseRoute::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     * @throws Exception\ExceptionInterface
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

        if (!isset($options['only_return_path']) || !$options['only_return_path']) {
            if (!isset($options['uri'])) {
                $uri = new HttpUri();

                if (isset($options['force_canonical']) && $options['force_canonical']) {
                    if ($this->requestUri === null) {
                        throw new Exception\RuntimeException('Request URI has not been set');
                    }

                    $uri->setScheme($this->requestUri->getScheme())
                        ->setHost($this->requestUri->getHost())
                        ->setPort($this->requestUri->getPort());
                }

                $options['uri'] = $uri;
            } else {
                $uri = $options['uri'];
            }

            $path = $this->baseUrl . $route->assemble(array_merge($this->defaultParams, $params), $options);

            if ((isset($options['force_canonical']) && $options['force_canonical']) || $uri->getHost() !== null) {
                if ($uri->getScheme() === null) {
                    if ($this->requestUri === null) {
                        throw new Exception\RuntimeException('Request URI has not been set');
                    }

                    $uri->setScheme($this->requestUri->getScheme());
                }

                return $uri->setPath($path)->toString();
            } elseif (!$uri->isAbsolute() && $uri->isValidRelative()) {
                return $uri->setPath($path)->toString();
            }
        }

        return $this->baseUrl . $route->assemble(array_merge($this->defaultParams, $params), $options);
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
     * @return TreeRouteStack
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
