<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Router\Http;

use Traversable;
use Zend\Mvc\Router\Exception;
use Zend\Mvc\Router\PriorityList;
use Zend\Mvc\Router\RoutePluginManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;

/**
 * RouteInterface part.
 *
 * @package    Zend_Mvc_Router
 * @subpackage Http
 * @see        http://guides.rubyonrails.org/routing.html
 */
class Part extends TreeRouteStack implements RouteInterface
{
    /**
     * RouteInterface to match.
     *
     * @var RouteInterface
     */
    protected $route;

    /**
     * Whether the route may terminate.
     *
     * @var bool
     */
    protected $mayTerminate;

    /**
     * Child routes.
     *
     * @var mixed
     */
    protected $childRoutes;

    /**
     * Create a new part route.
     *
     * @param  mixed              $route
     * @param  bool            $mayTerminate
     * @param  RoutePluginManager $routePlugins
     * @param  array|null         $childRoutes
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($route, $mayTerminate, RoutePluginManager $routePlugins, array $childRoutes = null)
    {
        $this->routePluginManager = $routePlugins;

        if (!$route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        if ($route instanceof self) {
            throw new Exception\InvalidArgumentException('Base route may not be a part route');
        }

        $this->route        = $route;
        $this->mayTerminate = $mayTerminate;
        $this->childRoutes  = $childRoutes;
        $this->routes       = new PriorityList();
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    Route::factory()
     * @param  mixed $options
     * @throws Exception\InvalidArgumentException
     * @return Part
     */
    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        if (!isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        if (!isset($options['route_plugins'])) {
            throw new Exception\InvalidArgumentException('Missing "route_plugins" in options array');
        }

        if (!isset($options['may_terminate'])) {
            $options['may_terminate'] = false;
        }

        if (!isset($options['child_routes']) || !$options['child_routes']) {
            $options['child_routes'] = null;
        }
        if ($options['child_routes'] instanceof Traversable) {
            $options['child_routes'] = ArrayUtils::iteratorToArray($options['child_routes']);
        }

        return new static($options['route'], $options['may_terminate'], $options['route_plugins'], $options['child_routes']);
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    Route::match()
     * @param  Request  $request
     * @param  int|null $pathOffset
     * @return RouteMatch|null
     */
    public function match(Request $request, $pathOffset = null)
    {
        if ($pathOffset === null) {
            $pathOffset = 0;
        }

        $match = $this->route->match($request, $pathOffset);

        if ($match !== null && method_exists($request, 'getUri')) {
            if ($this->childRoutes !== null) {
                $this->addRoutes($this->childRoutes);
                $this->childRoutes = null;
            }

            $nextOffset = $pathOffset + $match->getLength();

            $uri        = $request->getUri();
            $pathLength = strlen($uri->getPath());

            if ($this->mayTerminate && $nextOffset === $pathLength && trim($uri->getQuery()) == "") {
                return $match;
            }

            foreach ($this->routes as $name => $route) {
                if (($subMatch = $route->match($request, $nextOffset)) instanceof RouteMatch) {
                    if ($match->getLength() + $subMatch->getLength() + $pathOffset === $pathLength) {
                        return $match->merge($subMatch)->setMatchedRouteName($name);
                    }
                }
            }
        }

        return null;
    }

    /**
     * assemble(): Defined by RouteInterface interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     * @throws Exception\RuntimeException
     */
    public function assemble(array $params = array(), array $options = array())
    {
       if ($this->childRoutes !== null) {
            $this->addRoutes($this->childRoutes);
            $this->childRoutes = null;
        }

        $options['has_child'] = (isset($options['name']));

        $path   = $this->route->assemble($params, $options);
        $params = array_diff_key($params, array_flip($this->route->getAssembledParams()));

        if (!isset($options['name'])) {
            if (!$this->mayTerminate) {
                throw new Exception\RuntimeException('Part route may not terminate');
            } else {
                return $path;
            }
        }

        unset($options['has_child']);
        $options['only_return_path'] = true;
        $path .= parent::assemble($params, $options);

        return $path;
    }

    /**
     * getAssembledParams(): defined by RouteInterface interface.
     *
     * @see    Route::getAssembledParams
     * @return array
     */
    public function getAssembledParams()
    {
        // Part routes may not occur as base route of other part routes, so we
        // don't have to return anything here.
        return array();
    }
}
