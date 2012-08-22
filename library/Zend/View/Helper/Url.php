<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface;
use Zend\View\Exception;

/**
 * Helper for making easy links and getting urls that depend on the routes and router.
 *
 * @package    Zend_View
 * @subpackage Helper
 */
class Url extends AbstractHelper
{
    /**
     * RouteStackInterface instance.
     *
     * @var RouteStackInterface
     */
    protected $router;

    /**
     * RouteInterface match returned by the router.
     *
     * @var RouteMatch.
     */
    protected $routeMatch;

    /**
     * Set the router to use for assembling.
     *
     * @param RouteStackInterface $router
     * @return Url
     */
    public function setRouter(RouteStackInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Set route match returned by the router.
     *
     * @param  RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    /**
     * Generates an url given the name of a route.
     *
     * @see    Zend\Mvc\Router\RouteInterface::assemble()
     * @param  string  $name               Name of the route
     * @param  array   $params             Parameters for the link
     * @param  array   $options            Options for the route
     * @param  boolean $reuseMatchedParams Whether to reuse matched parameters
     * @return string Url                  For the link href attribute
     * @throws Exception\RuntimeException  If no RouteStackInterface was provided
     * @throws Exception\RuntimeException  If no RouteMatch was provided
     * @throws Exception\RuntimeException  If RouteMatch didn't contain a matched route name
     */
    public function __invoke($name = null, array $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        if (null === $this->router) {
            throw new Exception\RuntimeException('No RouteStackInterface instance provided');
        }

        if (3 == func_num_args() && is_bool($options)) {
            $reuseMatchedParams = $options;
            $options = array();
        }

        if ($name === null) {
            if ($this->routeMatch === null) {
                throw new Exception\RuntimeException('No RouteMatch instance provided');
            }

            $name = $this->routeMatch->getMatchedRouteName();

            if ($name === null) {
                throw new Exception\RuntimeException('RouteMatch does not contain a matched route name');
            }
        }

        if ($reuseMatchedParams && $this->routeMatch !== null) {
            $routeMatchParams = $this->routeMatch->getParams();

            if (isset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER])) {
                $routeMatchParams['controller'] = $routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER];
            }

            $params = array_merge($routeMatchParams, $params);
        }

        $options['name'] = $name;

        return $this->router->assemble($params, $options);
    }
}
