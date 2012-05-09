<?php

namespace Zend\Mvc;

use Zend\EventManager\Event,
    Zend\Stdlib\RequestInterface as Request,
    Zend\Stdlib\ResponseInterface as Response,
    Zend\View\Model\ModelInterface as Model,
    Zend\View\Model\ViewModel;

class MvcEvent extends Event
{
    /**#@+
     * Mvc events triggered by eventmanager
     */
    const EVENT_BOOTSTRAP      = 'bootstrap';
    const EVENT_DISPATCH       = 'dispatch';
    const EVENT_DISPATCH_ERROR = 'dispatch.error';
    const EVENT_FINISH         = 'finish';
    const EVENT_RENDER         = 'render';
    const EVENT_ROUTE          = 'route';
    /**#@-*/

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var Router\RouteStackInterface
     */
    protected $router;

    /**
     * @var Router\RouteMatch
     */
    protected $routeMatch;

    /**
     * @var Model
     */
    protected $viewModel;

    /**
     * Get router
     *
     * @return Router\RouteStackInterface
     */
    public function getRouter()
    {
        return $this->getParam('router');
    }

    /**
     * Set router
     *
     * @param Router\RouteStackInterface $router
     * @return MvcEvent
     */
    public function setRouter(Router\RouteStackInterface $router)
    {
        $this->setParam('router', $router);
        $this->router = $router;
        return $this;
    }

    /**
     * Get route match
     *
     * @return Router\RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->getParam('route-match');
    }

    /**
     * Set route match
     *
     * @param Router\RouteMatch $matches
     * @return MvcEvent
     */
    public function setRouteMatch(Router\RouteMatch $matches)
    {
        $this->setParam('route-match', $matches);
        $this->routeMatch = $matches;
        return $this;
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->getParam('request');
    }

    /**
     * Set request
     *
     * @param Request $request
     * @return MvcEvent
     */
    public function setRequest(Request $request)
    {
        $this->setParam('request', $request);
        $this->request = $request;
        return $this;
    }

    /**
     * Get response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->getParam('response');
    }

    /**
     * Set response
     *
     * @param Response $response
     * @return MvcEvent
     */
    public function setResponse(Response $response)
    {
        $this->setParam('response', $response);
        $this->response = $response;
        return $this;
    }

    /**
     * Set value for viewModel
     *
     * @param  Model viewModel
     * @return MvcEvent
     */
    public function setViewModel(Model $viewModel)
    {
        $this->viewModel = $viewModel;
        return $this;
    }

    /**
     * Get value for viewModel
     *
     * @return Model
     */
    public function getViewModel()
    {
        if (null === $this->viewModel) {
            $this->setViewModel(new ViewModel());
        }
        return $this->viewModel;
    }

    /**
     * Get result
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->getParam('__RESULT__');
    }

    /**
     * Set result
     *
     * @param mixed $result
     * @return MvcEvent
     */
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

    /**
     * Set controller
     *
     * @param $name
     * @return MvcEvent
     */
    public function setController($name)
    {
        $this->setParam('controller', $name);
        return $this;
    }

    /**
     * Get controller clas
     *
     * @return string
     */
    public function getControllerClass()
    {
        return $this->getParam('controller-class');
    }

    /**
     * Set controller class
     *
     * @param string $class
     * @return MvcEvent
     */
    public function setControllerClass($class)
    {
        $this->setParam('controller-class', $class);
        return $this;
    }
}
