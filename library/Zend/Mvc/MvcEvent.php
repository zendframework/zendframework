<?php

namespace Zend\Mvc;

use Zend\EventManager\Event,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response;

class MvcEvent extends Event
{
    protected $request;
    protected $response;
    protected $result;
    protected $router;
    protected $routeMatch;

    public function getRouter()
    {
        return $this->getParam('router');
    }

    public function setRouter(Router\RouteStack $router)
    {
        $this->setParam('router', $router);
        $this->router = $router;
        return $this;
    }

    public function getRouteMatch()
    {
        return $this->getParam('route-match');
    }

    public function setRouteMatch(Router\RouteMatch $matches)
    {
        $this->setParam('route-match', $matches);
        $this->routeMatch = $matches;
        return $this;
    }

    public function getRequest()
    {
        return $this->getParam('request');
    }

    public function setRequest(Request $request)
    {
        $this->setParam('request', $request);
        $this->request = $request;
        return $this;
    }

    public function getResponse()
    {
        return $this->getParam('response');
    }

    public function setResponse(Response $response)
    {
        $this->setParam('response', $response);
        $this->response = $response;
        return $this;
    }

    public function getResult()
    {
        return $this->getParam('__RESULT__');
    }

    public function setResult($result)
    {
        $this->setParam('__RESULT__', $result);
        $this->result = $result;
        return $this;
    }

    public function isError()
    {
        return $this->getParam('error', false);
    }

    public function setError($message)
    {
        $this->setParam('error', $message);
        return $this;
    }

    public function getError()
    {
        return $this->getParam('error', '');
    }

    public function getController()
    {
        return $this->getParam('controller');
    }

    public function setController($name)
    {
        $this->setParam('controller', $name);
        return $this;
    }

    public function getControllerClass()
    {
        return $this->getParam('controller-class');
    }

    public function setControllerClass($class)
    {
        $this->setParam('controller-class', $class);
        return $this;
    }
}
