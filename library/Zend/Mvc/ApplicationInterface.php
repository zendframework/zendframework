<?php

namespace Zend\Mvc;

use Zend\Di\LocatorInterface,
    Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerAware,
    Zend\EventManager\EventsCapableInterface,
    Zend\Stdlib\RequestInterface as Request,
    Zend\Stdlib\ResponseInterface as Response;

interface ApplicationInterface extends EventManagerAwareInterface, EventsCapableInterface
{
    /**
     * Set a service locator/DI object
     *
     * @param  LocatorInterface $locator
     * @return ApplicationInterface
     */
    public function setLocator(LocatorInterface $locator);

    /**
     * Set request object that will be consumed
     * 
     * @param  Request $request 
     * @return ApplicationInterface
     */
    public function setRequest(Request $request);

    /**
     * Set response object that will be returned
     * 
     * @param  Response $request 
     * @return ApplicationInterface
     */
    public function setResponse(Response $response);

    /**
     * Set the router used to decompose the request
     *
     * A router should return a metadata object containing a controller key.
     * 
     * @param  Router\RouteStackInterface $router
     * @return ApplicationInterface
     */
    public function setRouter(Router\RouteStackInterface $router);

    /**
     * Get the locator object
     * 
     * @return LocatorInterface
     */
    public function getLocator();

    /**
     * Get the request object
     *
     * @return Request
     */
    public function getRequest();

    /**
     * Get the response object
     *
     * @return Response
     */
    public function getResponse();

    /**
     * Get the router object
     *
     * @return Router
     */
    public function getRouter();

    /**
     * Run the application
     *
     * @return \Zend\Http\Response
     */
    public function run();
}
