<?php

namespace Zend\Mvc\Controller\Plugin;

use Zend\Mvc\InjectApplicationEvent,
    Zend\Mvc\Exception,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteStack;

class Url extends AbstractPlugin
{
    /**
     * Generates a URL based on a route
     * 
     * @param  string $route Route name
     * @param  array $params Parameters to use in url generation, if any
     * @param  array $options Route-specific options to use in url generation, if any
     * @return string
     * @throws Exception\DomainException if composed controller does not implement InjectApplicationEvent, or 
     *         router cannot be found in controller event
     */
    public function fromRoute($route, array $params = array(), array $options = array())
    {
        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEvent) {
            throw new Exception\DomainException('Url plugin requires a controller that implements InjectApplicationEvent');
        }

        $event  = $controller->getEvent();
        $router = null;
        if ($event instanceof MvcEvent) {
            $router = $event->getRouter();
        } elseif ($event instanceof Event) {
            $router = $event->getParam('router', false);
        }
        if (!$router instanceof RouteStack) {
            throw new Exception\DomainException('Url plugin requires that controller event compose a router; none found');
        }

        $options['name'] = $route;
        return $router->assemble($params, $options);
    }
}
