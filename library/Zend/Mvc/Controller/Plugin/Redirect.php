<?php

namespace Zend\Mvc\Controller\Plugin;

use Zend\Http\Response,
    Zend\Mvc\InjectApplicationEvent,
    Zend\Mvc\Exception,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteStack;

/**
 * @todo allow specifying status code as a default, or as an option to methods
 */
class Redirect extends AbstractPlugin
{
    protected $event;
    protected $response;
    protected $router;

    /**
     * Generates a URL based on a route
     *
     * @param  string $route Route name
     * @param  array $params Parameters to use in url generation, if any
     * @param  array $options Route-specific options to use in url generation, if any
     * @return Response
     * @throws Exception\DomainException if composed controller does not implement InjectApplicationEvent, or
     *         router cannot be found in controller event
     */
    public function toRoute($route, array $params = array(), array $options = array())
    {
        $response = $this->getResponse();
        $router   = $this->getRouter();

        $options['name'] = $route;
        $url = $router->assemble($params, $options);
        $response->headers()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);
        return $response;
    }

    /**
     * Redirect to the given URL
     *
     * @param  string $url
     * @return Response
     */
    public function toUrl($url)
    {
        $response = $this->getResponse();
        $response->headers()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);
        return $response;
    }

    /**
     * Get the router
     *
     * @return RouteStack
     * @throws Exception\DomainException if unable to find router
     */
    protected function getRouter()
    {
        if ($this->router) {
            return $this->router;
        }

        $event  = $this->getEvent();
        $router = $event->getRouter();
        if (!$router instanceof RouteStack) {
            throw new Exception\DomainException('Redirect plugin requires event compose a router');
        }
        $this->router = $router;
        return $this->router;
    }

    /**
     * Get the response
     *
     * @return Response
     * @throws Exception\DomainException if unable to find response
     */
    protected function getResponse()
    {
        if ($this->response) {
            return $this->response;
        }

        $event    = $this->getEvent();
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            throw new Exception\DomainException('Redirect plugin requires event compose a response');
        }
        $this->response = $response;
        return $this->response;
    }

    /**
     * Get the event
     *
     * @return MvcEvent
     * @throws Exception\DomainException if unable to find event
     */
    protected function getEvent()
    {
        if ($this->event) {
            return $this->event;
        }

        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEvent) {
            throw new Exception\DomainException('Redirect plugin requires a controller that implements InjectApplicationEvent');
        }

        $event = $controller->getEvent();
        if (!$event instanceof MvcEvent) {
            $params = $event->getParams();
            $event  = new MvcEvent();
            $event->setParams($params);
        }
        $this->event = $event;

        return $this->event;
    }
}
