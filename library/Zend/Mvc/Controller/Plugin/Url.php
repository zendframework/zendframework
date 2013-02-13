<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\EventManager\EventInterface;
use Zend\Mvc\Exception;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteStackInterface;

class Url extends AbstractPlugin
{
    /**
     * Generates a URL based on a route
     *
     * @param  string $route RouteInterface name
     * @param  array $params Parameters to use in url generation, if any
     * @param  array|bool $options RouteInterface-specific options to use in url generation, if any. If boolean, and no fourth argument, used as $reuseMatchedParams
     * @param  bool $reuseMatchedParams Whether to reuse matched parameters
     * @return string
     * @throws Exception\DomainException if composed controller does not implement InjectApplicationEventInterface, or
     *         router cannot be found in controller event
     * @throws Exception\RuntimeException if no RouteMatch instance or no matched route name present
     */
    public function fromRoute($route = null, array $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException('Url plugin requires a controller that implements InjectApplicationEventInterface');
        }

        $event   = $controller->getEvent();
        $router  = null;
        $matches = null;
        if ($event instanceof MvcEvent) {
            $router  = $event->getRouter();
            $matches = $event->getRouteMatch();
        } elseif ($event instanceof EventInterface) {
            $router  = $event->getParam('router', false);
            $matches = $event->getParam('route-match', false);
        }
        if (!$router instanceof RouteStackInterface) {
            throw new Exception\DomainException('Url plugin requires that controller event compose a router; none found');
        }

        if (3 == func_num_args() && is_bool($options)) {
            $reuseMatchedParams = $options;
            $options = array();
        }

        if ($route === null) {
            if (!$matches) {
                throw new Exception\RuntimeException('No RouteMatch instance present');
            }

            $route = $matches->getMatchedRouteName();

            if ($route === null) {
                throw new Exception\RuntimeException('RouteMatch does not contain a matched route name');
            }
        }

        if ($reuseMatchedParams && $matches) {
            $routeMatchParams = $matches->getParams();

            if (isset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER])) {
                $routeMatchParams['controller'] = $routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER];
                unset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER]);
            }

            if (isset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE])) {
                unset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE]);
            }

            $params = array_merge($routeMatchParams, $params);
        }

        $options['name'] = $route;
        return $router->assemble($params, $options);
    }
}
